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

        return view('admincabang.refunds.create', compact('order'));
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

            $totalRefund = 0;

            // 1️⃣ Buat refund utama dulu (total nanti diupdate)
            $refund = Refund::create([
                'order_id'    => $order->id,
                'cashier_id'  => auth()->id(),
                'reason'      => $request->reason,
                'total_refund' => 0,
                'status'      => 'approved',
            ]);

            foreach ($request->items as $itemId => $qty) {

                if ($qty <= 0) continue;

                $orderItem = $order->items()->where('id', $itemId)->first();
                if (!$orderItem) continue;

                if ($qty > $orderItem->quantity) {
                    throw new \Exception("Qty refund melebihi jumlah pembelian.");
                }

                $subtotal = ($orderItem->subtotal / $orderItem->quantity) * $qty;
                $totalRefund += $subtotal;

                // balikin stok
                $branchProduct = BranchProduct::where('branch_id', $order->branch_id)
                    ->where('product_id', $orderItem->product_id)
                    ->first();

                if ($branchProduct) {
                    $branchProduct->increment('stock', $qty);
                }

                // simpan refund item
                RefundItem::create([
                    'refund_id' => $refund->id,
                    'order_item_id' => $orderItem->id,
                    'type' => 'return',
                    'qty' => $qty,
                    'amount' => $subtotal,
                ]);
            }

            // update total refund
            $refund->update([
                'total_refund' => $totalRefund
            ]);
            $paymentMethod = optional($order->transaction)->payment_method ?? 'cash';

            RefundTransaction::create([
                'refund_id'     => $refund->id,
                'payment_method' => $paymentMethod,
                'amount'        => $order->total,
                'status'        => 'refunded',
                'refunded_at'   => now(),
                'processed_by'  => auth()->id(),
            ]);


            // kalau full refund, update status order
            if ($totalRefund >= $order->total) {
                $order->update([
                    'status' => 'cancelled'
                ]);
            }
        });

        return redirect()->route('admincabang.orders.index')
            ->with('success', 'Refund berhasil diproses.');
    }
}
