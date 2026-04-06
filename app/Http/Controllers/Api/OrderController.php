<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\VariantOption;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.details' => 'nullable|array'
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

            $product = Product::findOrFail($item['product_id']);

            $basePrice = $product->price;
            $variantPrice = 0;
            $variantIds = [];

            // 🔥 HANDLE VARIANT (DETAILS)
            if (!empty($item['details'])) {
                foreach ($item['details'] as $detail) {
                    $opt = VariantOption::find($detail['variant_option_id']);

                    if ($opt) {
                        $variantPrice += $opt->price_impact;
                        $variantIds[] = $opt->id;
                    }
                }
            }

            sort($variantIds);

            $finalPrice = $basePrice + $variantPrice;
            $subtotal = $finalPrice * $item['qty'];

            // 🔥 CREATE ORDER ITEM
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['qty'],
                'price' => $finalPrice,
                'subtotal' => $subtotal
            ]);

            // 🔥 SIMPAN DETAIL VARIANT
            if (!empty($item['details'])) {
                foreach ($item['details'] as $detail) {
                    $opt = VariantOption::find($detail['variant_option_id']);

                    if ($opt) {
                        $orderItem->details()->create([
                            'variant_type_id' => $opt->variant_type_id,
                            'variant_option_id' => $opt->id,
                            'price_impact' => $opt->price_impact,
                        ]);
                    }
                }
            }

            $total += $subtotal;
        }

        $order->update(['total' => $total]);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil',
            'data' => $order->load(
                'items.details.variantOption',
                'items.product'
            )
        ]);
    }
}
