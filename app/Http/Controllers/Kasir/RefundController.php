<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\BranchProduct;
use App\Models\Order;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\RefundTransaction;
use App\Models\Product; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    public function create(Order $order)
    {
        // Pastikan hanya status 'settlement' yang bisa refund
        if ($order->payment_status !== 'settlement') {
            return back()->with('error', 'Hanya order lunas yang bisa direfund.');
        }

        // Gunakan logika gt(now()->subMinutes(10)) agar konsisten dengan view
        if (!$order->created_at->gt(now()->subMinutes(10))) {
            return back()->with('error', 'Waktu refund sudah habis (maks 10 menit).');
        }

        // Pastikan load sampai ke variantOption
        $order->load([
            'items.product',
            'items.refundItems',
            'items.details.variantOption' // <--- Ini kuncinya
        ]);

        $branchProducts = BranchProduct::with('product')
            ->where('branch_id', $order->branch_id)
            ->get();

        return view('admincabang.refunds.create', compact('order', 'branchProducts'));
    }

    public function store(Request $request, Order $order)
    {
        if (!auth()->user()->hasRole('admincabang')) abort(403);

        $request->validate([
            'reason' => 'required|string',
            'items'  => 'required|array',
            'items.*.qty' => 'nullable|integer|min:0', // Izinkan 0 atau kosong
            'items.*.type' => 'required|in:return,exchange',
            'items.*.exchange_product_id' => 'required_if:items.*.type,exchange',
        ]);

        // LOGIKA TAMBAHAN: Pastikan setidaknya ada satu produk yang qty-nya > 0
        $anyItemToRefund = false;
        foreach ($request->items as $item) {
            if (($item['qty'] ?? 0) > 0) {
                $anyItemToRefund = true;
                break;
            }
        }

        if (!$anyItemToRefund) {
            return back()->with('error', 'Pilih minimal satu item dengan jumlah lebih dari 0 untuk direfund.');
        }

        try {
        DB::beginTransaction();

        $totalRefundCashToCustomer = 0; // Uang yang keluar dari kasir
        $hasValidItem = false;

        // 1. Buat Header Refund
        $refund = Refund::create([
            'order_id'     => $order->id,
            'cashier_id'   => auth()->id(),
            'reason'       => $request->reason,
            'total_refund' => 0, // Akan diupdate di bawah
            'status'       => 'approved',
        ]);

        foreach ($request->items as $itemId => $data) {
            $qty = $data['qty'] ?? 0;
            if ($qty <= 0) continue;

            $hasValidItem = true;
            $orderItem = $order->items()->findOrFail($itemId);

            // VALIDASI: Cek sisa Qty yang bisa direfund (mencegah double refund)
            $alreadyRefunded = RefundItem::where('order_item_id', $orderItem->id)->sum('qty');
            if ($qty > ($orderItem->quantity - $alreadyRefunded)) {
                throw new \Exception("Jumlah refund item {$orderItem->product->name} melebihi sisa beli.");
            }

            $type = $data['type'] ?? 'return';
            $oldPrice = $orderItem->price;
            $itemRefundValue = 0;

            // 2. Kembalikan stok item lama ke branch
            $originalBP = BranchProduct::where('branch_id', $order->branch_id)
                ->where('product_id', $orderItem->product_id)->first();
            if ($originalBP) $originalBP->increment('stock', $qty);

            // 3. Logika Exchange (Tukar Barang)
            if ($type === 'exchange') {
                $newProduct = Product::findOrFail($data['exchange_product_id']);
                $newPrice = $newProduct->price;

                $exchangeBP = BranchProduct::where('branch_id', $order->branch_id)
                    ->where('product_id', $newProduct->id)->first();

                if (!$exchangeBP || $exchangeBP->stock < $qty) {
                    throw new \Exception("Stok produk pengganti ({$newProduct->name}) tidak cukup.");
                }
                $exchangeBP->decrement('stock', $qty);

                // Jika lebih murah, cafe balikin duit selisihnya
                if ($newPrice < $oldPrice) {
                    $itemRefundValue = ($oldPrice - $newPrice) * $qty;
                }
            } else {
                // Tipe RETURN: Uang kembali full (Harga Item * Qty)
                $itemRefundValue = $oldPrice * $qty;
            }

            // 4. Simpan detail item refund
            RefundItem::create([
                'refund_id' => $refund->id,
                'order_item_id' => $orderItem->id,
                'type' => $type,
                'qty' => $qty,
                'amount' => $itemRefundValue,
                'exchange_product_id' => ($type === 'exchange') ? $data['exchange_product_id'] : null,
            ]);

            $totalRefundCashToCustomer += $itemRefundValue;
        }

        if (!$hasValidItem) throw new \Exception("Pilih minimal 1 item untuk direfund.");

        // 5. Update total uang yang direfund di header
        $refund->update(['total_refund' => $totalRefundCashToCustomer]);

        // --- TAMBAHKAN LOGIKA DECREMENT DI SINI ---
        // Ini yang membuat dashboard kamu sinkron. Mengurangi total di orders.
        if ($totalRefundCashToCustomer > 0) {
            $order->decrement('total', $totalRefundCashToCustomer);
        }
        // ------------------------------------------

        // 6. Catat Transaksi Keluar (Arus Kas Keluar)
        if ($totalRefundCashToCustomer > 0) {
            RefundTransaction::create([
                'refund_id' => $refund->id,
                'payment_method' => 'cash',
                'amount' => $totalRefundCashToCustomer,
                'status' => 'refunded',
                'refunded_at' => now(),
                'processed_by' => auth()->id(),
            ]);
        }

        // 7. LOGIKA UPDATE STATUS ORDER (Partial vs Full)
        $totalOriginalQty = $order->items->sum('quantity');
        $totalRefundedQty = RefundItem::whereHas('refund', function ($q) use ($order) {
            $q->where('order_id', $order->id);
        })->sum('qty');

        if ($totalRefundedQty >= $totalOriginalQty) {
            // Jika semua item habis direfund, status jadi Cancelled & Payment Refund
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'refund'
            ]);
        } else {
            // Jika masih ada sisa item, tetap Settlement agar sisa uang tercatat sebagai pendapatan
            $order->update(['payment_status' => 'settlement']);
        }

        DB::commit();
        return redirect()->route('admincabang.orders.index')->with('success', 'Proses refund berhasil dicatat dan total pendapatan telah diperbarui.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
    }
}
