<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EccommerceController extends Controller
{
    /**
     * ============================
     *  HELPER METHODS (PRIVATE)
     * ============================
     */

    private function findOrCreatePendingOrder()
    {
        return Order::firstOrCreate(
            ['user_id' => Auth::id(), 'status' => 'pending'],
            ['total' => 0]
        );
    }

    private function normalizeVariantDetails($variants)
    {
        if (empty($variants)) return json_encode([]);

        $details = collect($variants)
            ->map(fn($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'type' => $v->type,
                'impact' => $v->price_impact
            ])
            ->sortBy('id') // agar konsisten dan bisa cek duplikat
            ->values()
            ->all();

        return json_encode($details);
    }

    private function calculateItemPrice($product, $variants, $qty)
    {
        $variantImpact = $variants->sum('price_impact');
        $unitPrice = $product->price + $variantImpact;
        return [
            'unit' => $unitPrice,
            'total' => $unitPrice * $qty
        ];
    }

    private function recalculateOrderTotal($order)
    {
        $order->total = $order->items()->sum('price');
        $order->save();
    }

    private function findExistingItem($order, $product, $variantJson, $item)
    {
        return OrderItem::where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->where('temperature', $item['temperature'] ?? 'Iced')
            ->where('sugar_level', $item['sugar_level'] ?? 'Normal')
            ->where('ice_level', $item['ice_level'] ?? 'Normal')
            ->where('variant_details', $variantJson)
            ->first();
    }


    /**
     * ============================
     *         MAIN METHODS
     * ============================
     */

    public function index()
    {
        $category = Category::all();
        $product = Product::all();
        $latestOrder = $this->getCart();
        return view('welcome', compact('category', 'product', 'latestOrder'));
    }

    public function getCart()
    {
        if (!Auth::check()) {
            return null;
        }

        return Order::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->with('items.product')
            ->latest()
            ->first();
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'temperature' => 'nullable|in:Hot,Iced',
            'sugar_level' => 'nullable|in:Normal,Less Sugar,No Sugar',
            'ice_level' => 'nullable|in:Normal,Less Ice,No Ice',
            'variants' => 'nullable|array',
            'variants.*' => 'exists:product_variants,id',
        ]);

        $item = $request->only([
            'product_id',
            'quantity',
            'temperature',
            'sugar_level',
            'ice_level'
        ]);

        try {
            DB::beginTransaction();

            $order = $this->findOrCreatePendingOrder();
            $product = Product::findOrFail($item['product_id']);
            $qty = $item['quantity'];

            // Get variants
            $variantModels = ProductVariant::whereIn('id', $request->variants ?? [])->get();

            // Normalize JSON variant detail for duplicate check
            $variantJson = $this->normalizeVariantDetails($variantModels);

            // Calculate price
            $prices = $this->calculateItemPrice($product, $variantModels, $qty);

            // Cek duplikasi item
            $existingItem = $this->findExistingItem($order, $product, $variantJson, $item);

            if ($existingItem) {
                $existingItem->quantity += $qty;
                $existingItem->price += $prices['total'];
                $existingItem->save();
            } else {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $prices['total'],
                    'variant_details' => $variantJson,
                    'temperature' => $item['temperature'] ?? 'Iced',
                    'sugar_level' => $item['sugar_level'] ?? 'Normal',
                    'ice_level' => $item['ice_level'] ?? 'Normal',
                ]);
            }

            // Update total
            $this->recalculateOrderTotal($order);

            DB::commit();

            // Update offcanvas cart HTML
            $latestOrder = $this->getCart();
            $cartHtml = view('partials.offcanvas-cart-content', [
                'latestOrder' => $latestOrder
            ])->render();

            return response()->json([
                'status' => 'success',
                'message' => "$qty x {$product->name} berhasil ditambahkan ke keranjang",
                'cartHtml' => $cartHtml,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan ke keranjang. ' . $e->getMessage()
            ], 500);
        }
    }


    public function myOrders()
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function orderDetail($id)
    {
        $order = Order::with(['items.product'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('orders.detail', compact('order'));
    }

    public function updateQuantity(Request $request)
    {
        try {
            $request->validate([
                'order_product_id' => 'required|exists:order_items,id',
                'quantity' => 'required|integer|min:1',
            ]);

            DB::transaction(function () use ($request) {
                $item = OrderItem::findOrFail($request->order_product_id);
                $product = Product::findOrFail($item->product_id);
                $order = Order::findOrFail($item->order_id);

                if ($order->user_id != Auth::id()) {
                    throw new \Exception('Akses Tidak Sah.');
                }

                if ($order->status !== 'pending') {
                    throw new \Exception('Tidak dapat mengubah pesanan yang sudah selesai.');
                }

                if ($request->quantity > $product->stok) {
                    throw new \Exception("Stok tersisa {$product->stok}.");
                }

                $unitPrice = $item->price / $item->quantity;
                $item->quantity = $request->quantity;
                $item->price = $unitPrice * $request->quantity;
                $item->save();

                $this->recalculateOrderTotal($order);
            });

            return back()->with("success", 'Jumlah Produk berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function removeItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:order_items,id',
        ]);

        try {
            $item = OrderItem::findOrFail($request->item_id);
            $order = $item->order;

            if ($order->user_id != Auth::id() || $order->status !== 'pending') {
                return response()->json(['status' => 'error', 'message' => 'Akses ditolak.']);
            }

            $item->delete();

            // Update total
            $this->recalculateOrderTotal($order);

            // Ambil cart terbaru untuk render ulang Offcanvas
            $latestOrder = $this->getCart();
            $cartHtml = view('partials.offcanvas-cart-content', [
                'latestOrder' => $latestOrder
            ])->render();

            return response()->json([
                'status' => 'success',
                'cartHtml' => $cartHtml,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }




    public function checkOut(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $order = Order::with('items.product')->findOrFail($request->order_id);

                if ($order->user_id != Auth::id()) {
                    return redirect()->route('orders.my')->with('error', 'Akses Tidak Sah.');
                }

                if ($order->status !== 'pending') {
                    return redirect()->route('orders.detail', $order->id)->with('error', 'Pesanan sudah selesai.');
                }

                if ($order->items->isEmpty()) {
                    return redirect()->route('orders.my')->with('error', 'Pesanan kosong.');
                }

                $noStock = [];
                foreach ($order->items as $item) {
                    if ($item->quantity > $item->product->stok) {
                        $noStock[] = "{$item->product->name} (butuh {$item->quantity}, stok {$item->product->stok})";
                    }
                }

                if (!empty($noStock)) {
                    return redirect()
                        ->route('orders.detail', $order->id)
                        ->with('error', "Stok tidak cukup untuk: " . implode(', ', $noStock));
                }

                foreach ($order->items as $item) {
                    $product = $item->product;
                    $product->stok -= $item->quantity;
                    $product->save();
                }

                $order->status = 'completed';
                $order->save();

                return redirect()->route('orders.detail', $order->id)
                    ->with('success', 'Checkout Berhasil!');
            });
        } catch (\Exception $e) {
            return redirect()->route('orders.my')
                ->with('error', 'Kesalahan checkout: ' . $e->getMessage());
        }
    }
}
