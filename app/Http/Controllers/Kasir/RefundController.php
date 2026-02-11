<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\BranchProduct;
use App\Models\Order;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\RefundTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    public function create(Order $order)
    {
        if ($order->payment_status !== 'settlement') {
            return back()->with('error', 'Order belum dibayar.');
        }

        if (now()->diffInMinutes($order->created_at) > 10) {
            return back()->with('error', 'Waktu refund sudah lewat 10 menit.');
        }
        $branchProducts = BranchProduct::with('product')
            ->where('branch_id', $order->branch_id)
            ->get();

        // Kirim variabel branchProducts ke view
        return view('admincabang.refunds.create', compact('order', 'branchProducts'));
    }


    public function store(Request $request, Order $order)
    {
        if (!auth()->user()->hasRole('admincabang')) {
            abort(403);
        }

        if ($order->payment_status !== 'settlement') {
            return back()->with('error', 'Order belum dibayar.');
        }

        if (now()->diffInMinutes($order->created_at) > 10) {
            return back()->with('error', 'Waktu refund sudah lewat 10 menit.');
        }

        $request->validate([
            'reason' => 'required|string',
            'items'  => 'required|array'
        ]);

        DB::transaction(function () use ($request, $order) {
            $totalRefundValue = 0;

            $refund = Refund::create([
                'order_id'   => $order->id,
                'cashier_id' => auth()->id(),
                'reason'     => $request->reason,
                'total_refund' => 0,
                'status'     => 'approved',
            ]);

            foreach ($request->items as $itemId => $data) {
                $qty = $data['qty'] ?? 0;
                if ($qty <= 0) continue;

                $orderItem = $order->items()->findOrFail($itemId);
                $type = $data['type'] ?? 'return';

                // Hitung nilai nominal item yang bermasalah
                $subtotal = ($orderItem->price) * $qty;

                // 1. Balikin stok item yang salah/rusak ke branch (Return to Stock)
                $originalProduct = BranchProduct::where('branch_id', $order->branch_id)
                    ->where('product_id', $orderItem->product_id)->first();
                if ($originalProduct) $originalProduct->increment('stock', $qty);

                // 2. Jika Exchange, potong stok produk pengganti
                if ($type === 'exchange' && !empty($data['exchange_product_id'])) {
                    $exchangeProduct = BranchProduct::where('branch_id', $order->branch_id)
                        ->where('product_id', $data['exchange_product_id'])->first();

                    if (!$exchangeProduct || $exchangeProduct->stock < $qty) {
                        throw new \Exception("Stok produk pengganti tidak cukup.");
                    }
                    $exchangeProduct->decrement('stock', $qty);
                }

                // 3. Simpan Detail Item Refund
                RefundItem::create([
                    'refund_id' => $refund->id,
                    'order_item_id' => $orderItem->id,
                    'type' => $type,
                    'qty' => $qty,
                    'amount' => ($type === 'return') ? $subtotal : 0, // Nilai uang kembali 0 jika tukar barang
                    'exchange_product_id' => ($type === 'exchange') ? $data['exchange_product_id'] : null,
                ]);

                if ($type === 'return') {
                    $totalRefundValue += $subtotal;
                }
            }

            // Update total uang yang benar-benar keluar dari laci kasir
            $refund->update(['total_refund' => $totalRefundValue]);

            // Simpan transaksi kas keluar hanya jika ada uang yang kembali
            if ($totalRefundValue > 0) {
                RefundTransaction::create([
                    'refund_id' => $refund->id,
                    'payment_method' => optional($order->transaction)->payment_method ?? 'cash',
                    'amount' => $totalRefundValue,
                    'status' => 'refunded',
                    'refunded_at' => now(),
                    'processed_by' => auth()->id(),
                ]);
            }

            // Update payment_status order agar tombol refund hilang
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'refund'
            ]);
        });

        return redirect()->route('admincabang.orders.index')->with('success', 'Refund/Tukar barang berhasil.');
    }
}
