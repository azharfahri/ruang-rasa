<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('branch_id', auth()->user()->branch_id)->latest()->get();
        return view('kasir.orders.index', compact('orders'));
    }

    public function create()
    {
        $order = Order::firstOrCreate(
            [
                'branch_id'      => auth()->user()->branch_id,
                'cashier_id'     => auth()->id(),
                'status'         => 'pending',
                'payment_status' => 'pending',
            ],
            [
                'order_type' => 'offline_dinein',
                'total'      => 0,
            ]
        );

        $products = Product::all();
        return view('kasir.orders.create', compact('order','products'));
    }

    public function addItem(Request $request, Order $order)
    {
        $product = Product::findOrFail($request->product_id);

        $qty = 1;
        $subtotal = $product->price * $qty;

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => $qty,
            'price'      => $product->price,
            'subtotal'   => $subtotal,
        ]);

        $order->update([
            'total' => $order->items()->sum('subtotal')
        ]);

        return back();
    }

    public function payCash(Order $order)
    {
        DB::transaction(function () use ($order) {

            Transaction::create([
                'order_id'        => $order->id,
                'payment_gateway' => 'cash',
                'payment_method'  => 'cash',
                'amount'          => $order->total,
                'status'          => 'paid',
            ]);

            $order->update([
                'status' => 'processing',
                'payment_status' => 'settlement',
            ]);
        });

        return redirect()->route('cashier.orders.index');
    }

    public function history()
    {
        $orders = Order::where('branch_id', auth()->user()->branch_id)
            ->where('payment_status','settlement')
            ->latest()
            ->get();

        return view('kasir.orders.history', compact('orders'));
    }
}
