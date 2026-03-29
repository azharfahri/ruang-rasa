<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\VariantOption;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.variants' => 'nullable|array'
        ]);

        $user = auth()->user();

        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'branch_id' => $request->branch_id,
            'total' => 0,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        $total = 0;

        foreach ($request->items as $item) {

            $product = Product::with('variantTypes.options')->find($item['product_id']);

            // 🔥 ambil base price
            $basePrice = $product->price;

            $variantPrice = 0;
            $variantIds = [];

            if (!empty($item['variants'])) {
                $options = VariantOption::whereIn('id', $item['variants'])->get();

                foreach ($options as $opt) {
                    $variantPrice += $opt->price_impact;
                    $variantIds[] = $opt->id;
                }
            }

            sort($variantIds);

            $finalPrice = $basePrice + $variantPrice;
            $subtotal = $finalPrice * $item['qty'];

            // 🔥 create item
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['qty'],
                'price' => $finalPrice,
                'subtotal' => $subtotal
            ]);

            // 🔥 simpan variant detail
            if (!empty($item['variants'])) {
                foreach ($options as $opt) {
                    $orderItem->details()->create([
                        'variant_type_id' => $opt->variant_type_id,
                        'variant_option_id' => $opt->id,
                        'price_impact' => $opt->price_impact,
                    ]);
                }
            }

            $total += $subtotal;
        }

        $order->update(['total' => $total]);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil',
            'data' => $order->load('items.details.variantOption', 'items.product')
        ]);
    }

    public function payMidtrans($id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        if ($order->order_type !== 'online_pickup') {
            return response()->json([
                'message' => 'Order ini tidak bisa bayar online'
            ], 400);
        }

        $items = [];
        $total = 0;

        foreach ($order->items as $item) {
            $price = (int) $item->price;
            $qty = (int) $item->quantity;

            $items[] = [
                'id' => $item->product_id,
                'price' => $price,
                'quantity' => $qty,
                'name' => $item->product->name ?? 'Menu'
            ];

            $total += $price * $qty;
        }

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $order->id . '-' . time(),
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name ?? 'Customer',
            ],
            'item_details' => $items,
        ];
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');

        try {
            $snapToken = Snap::getSnapToken($params);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }

        $order->update([
            'midtrans_order_id' => $params['transaction_details']['order_id']
        ]);

        return response()->json([
            'snap_token' => $snapToken
        ]);
    }

    // 📜 RIWAYAT ORDER
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items.product')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
