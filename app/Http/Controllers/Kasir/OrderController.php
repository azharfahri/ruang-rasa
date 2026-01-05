<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\VariantOption;
use App\Models\BranchProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('branch_id', auth()->user()->branch_id)
            ->latest()
            ->get();

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


        $products = Product::with(['variantTypes.options', 'branchProducts' => function ($q) {
            $q->where('branch_id', auth()->user()->branch_id)
                ->where('status', 'available');
        }])
            ->whereHas('branchProducts', function ($q) {
                $q->where('branch_id', auth()->user()->branch_id)
                    ->where('status', 'available');
            })
            ->get();

        return view('kasir.orders.create', compact('order', 'products'));
    }

    public function addItem(Request $request, Order $order)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
            'variants'   => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $order) {

            $branchId = auth()->user()->branch_id;

            $branchProduct = BranchProduct::where('branch_id', $branchId)
                ->where('product_id', $request->product_id)
                ->where('status', 'available')
                ->firstOrFail();

            $basePrice = $branchProduct->price_override
                ?? $branchProduct->product->price;

            $variantPrice = 0;
            $variantIds   = [];

            if ($request->variants) {
                $optionIds = collect($request->variants)->flatten()->toArray();

                $options = VariantOption::whereIn('id', $optionIds)
                    ->whereHas('variantType', function ($q) use ($request) {
                        $q->where('product_id', $request->product_id);
                    })
                    ->get();

                foreach ($options as $option) {
                    $variantPrice += $option->price_impact;
                    $variantIds[] = $option->id;
                }
            }

            sort($variantIds);

            $finalPrice = $basePrice + $variantPrice;

            $existingItem = $order->items()
                ->with('details')
                ->where('product_id', $request->product_id)
                ->get()
                ->first(function ($item) use ($variantIds) {
                    return $item->details
                        ->pluck('variant_option_id')
                        ->sort()
                        ->values()
                        ->toArray() === $variantIds;
                });

            if ($existingItem) {
                $existingItem->increment('quantity', $request->qty);
                $existingItem->update([
                    'subtotal' => $existingItem->quantity * $existingItem->price,
                ]);
            } else {
                $item = OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $request->product_id,
                    'quantity'   => $request->qty,
                    'price'      => $finalPrice,
                    'subtotal'   => $finalPrice * $request->qty,
                ]);

                foreach ($variantIds as $id) {
                    $option = VariantOption::find($id);
                    $item->details()->create([
                        'variant_type_id'   => $option->variant_type_id,
                        'variant_option_id' => $option->id,
                        'price_impact'      => $option->price_impact,
                    ]);
                }
            }

            $order->update([
                'total' => $order->items()->sum('subtotal'),
            ]);
        });

        return back();
    }

    public function minusItem(Order $order, OrderItem $item)
    {
        abort_if($item->order_id !== $order->id, 403);

        DB::transaction(function () use ($order, $item) {

            if ($item->quantity > 1) {
                $item->decrement('quantity');
                $item->update([
                    'subtotal' => $item->quantity * $item->price,
                ]);
            } else {
                $item->details()->delete();
                $item->delete();
            }

            $order->update([
                'total' => $order->items()->sum('subtotal'),
            ]);
        });

        return back();
    }

    public function updateItemVariant(Request $request, Order $order, OrderItem $item)
    {
        abort_if($order->status !== 'pending', 403);
        abort_if($item->order_id !== $order->id, 403);

        $request->validate([
            'variants' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $order, $item) {

            // ambil base price produk
            $branchProduct = BranchProduct::where('branch_id', auth()->user()->branch_id)
                ->where('product_id', $item->product_id)
                ->firstOrFail();

            $basePrice = $branchProduct->price_override
                ?? $branchProduct->product->price;

            // hapus detail lama
            $item->details()->delete();

            $variantPrice = 0;

            if ($request->variants) {
                $optionIds = collect($request->variants)->values()->toArray();

                $options = VariantOption::whereIn('id', $optionIds)
                    ->whereHas('variantType', function ($q) use ($item) {
                        $q->where('product_id', $item->product_id);
                    })
                    ->get();

                foreach ($options as $option) {
                    $variantPrice += $option->price_impact;

                    $item->details()->create([
                        'variant_type_id'   => $option->variant_type_id,
                        'variant_option_id' => $option->id,
                        'price_impact'      => $option->price_impact,
                    ]);
                }
            }

            $finalPrice = $basePrice + $variantPrice;

            // update item
            $item->update([
                'price'    => $finalPrice,
                'subtotal' => $finalPrice * $item->quantity,
            ]);

            // update total order
            $order->update([
                'total' => $order->items()->sum('subtotal'),
            ]);
        });

        return back();
    }


    public function payCash(Order $order)
    {
        abort_if($order->total <= 0, 400);

        DB::transaction(function () use ($order) {

            Transaction::create([
                'order_id'        => $order->id,
                'payment_gateway' => 'cash',
                'payment_method'  => 'cash',
                'amount'          => $order->total,
                'status'          => 'paid',
            ]);

            $order->update([
                'status'         => 'processing',
                'payment_status' => 'settlement',
            ]);
        });

        return redirect()->route('cashier.orders.index');
    }

    public function history()
    {
        $orders = Order::where('branch_id', auth()->user()->branch_id)
            ->where('payment_status', 'settlement')
            ->latest()
            ->get();

        return view('kasir.orders.history', compact('orders'));
    }
}
