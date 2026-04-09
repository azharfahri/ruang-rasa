<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\VariantOption;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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

        try {
            $order = DB::transaction(function () use ($request) {
                $user = auth()->user();

                // 1. Generate Pickup Code Unik
                $pickupCode = 'RR-' . strtoupper(Str::random(3));
                while (Order::where('pickup_code', $pickupCode)->whereDate('created_at', today())->exists()) {
                    $pickupCode = 'RR-' . strtoupper(Str::random(3));
                }

                // 2. Buat Order
                $order = Order::create([
                    'user_id' => $user->id,
                    'customer_name' => $user->name,
                    'branch_id' => $request->branch_id,
                    'total' => 0,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'pickup_code' => $pickupCode, // <--- Simpan di sini
                ]);

                $total = 0;

                foreach ($request->items as $item) {
                    $branchProduct = DB::table('branch_products')
                        ->where('branch_id', $request->branch_id)
                        ->where('product_id', $item['product_id'])
                        ->first();

                    if (!$branchProduct) {
                        throw new \Exception("Produk tidak tersedia di cabang ini");
                    }

                    $product = Product::findOrFail($item['product_id']);

                    // pake override kalau ada
                    $basePrice = $branchProduct->price_override ?? $product->price;
                    $variantPrice = 0;

                    // Handle Variant
                    if (!empty($item['details'])) {
                        $optionIds = collect($item['details'])->pluck('variant_option_id')->toArray();
                        $options = VariantOption::whereIn('id', $optionIds)->get();

                        foreach ($options as $opt) {
                            $variantPrice += $opt->price_impact;
                        }
                    }

                    $finalPrice = $basePrice + $variantPrice;
                    $subtotal = $finalPrice * $item['qty'];

                    // Create Order Item
                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['qty'],
                        'price' => $finalPrice,
                        'subtotal' => $subtotal
                    ]);

                    // Simpan Detail Variant
                    if (!empty($item['details']) && isset($options)) {
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
                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil',
                'data' => $order->load(
                    'items.details.variantOption',
                    'items.product'
                )
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        // Mengambil user yang sedang login
        $user = auth()->user();

        // Mengambil semua order milik user tersebut, urutkan dari yang terbaru
        $orders = Order::with([
            'items.product',
            'items.details.variantOption'
        ])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar riwayat pesanan berhasil dimuat',
            'data' => $orders
        ]);
    }
}
