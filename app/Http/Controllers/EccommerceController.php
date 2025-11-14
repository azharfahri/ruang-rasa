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
        // Validasi disederhanakan untuk payload single item dari JS modal
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'temperature' => 'nullable|in:Hot,Iced',
            'sugar_level' => 'nullable|in:Normal,Less Sugar,No Sugar',
            'ice_level' => 'nullable|in:Normal,Less Ice,No Ice',
            // Name input varian di JS adalah 'variants'
            'variants' => 'nullable|array',
            'variants.*' => 'exists:product_variants,id',
        ]);

        // Menggunakan variabel tunggal untuk item
        $item = $request->only([
            'product_id',
            'quantity',
            'temperature',
            'sugar_level',
            'ice_level'
        ]);
        $selectedVariants = $request->input('variants', []);

        try {
            DB::beginTransaction();

            // 1. Dapatkan atau buat Order (Keranjang)
            $order = Order::firstOrCreate(
                ['user_id' => Auth::id(), 'status' => 'pending'],
                ['total' => 0]
            );

            // 2. Ambil data produk
            $product = Product::findOrFail($item['product_id']);
            $quantity = $item['quantity'];
            $basePrice = $product->price;
            $variantTotalImpact = 0;
            $variantDetails = [];

            // 3. Hitung impact varian
            if (!empty($selectedVariants)) {
                $variants = ProductVariant::whereIn('id', $selectedVariants)->get();
                foreach ($variants as $variant) {
                    $variantTotalImpact += $variant->price_impact;
                    $variantDetails[] = [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'type' => $variant->type,
                        'impact' => $variant->price_impact,
                    ];
                }
            }

            $finalItemPrice = $basePrice + $variantTotalImpact;
            $itemTotalPrice = $finalItemPrice * $quantity;

            // KONSISTENSI: Pastikan urutan variant_details selalu sama untuk pengecekan duplikat
            $jsonVariantDetails = json_encode($variantDetails);

            // 4. Cek apakah item sama udah ada di keranjang
            $existingItem = OrderItem::where('order_id', $order->id)
                ->where('product_id', $product->id)
                ->where('temperature', $item['temperature'] ?? 'Iced')
                ->where('sugar_level', $item['sugar_level'] ?? 'Normal')
                ->where('ice_level', $item['ice_level'] ?? 'Normal')
                ->where('variant_details', $jsonVariantDetails)
                ->first();

            if ($existingItem) {
                // Item sama ditemukan, tambahkan kuantitas dan harga total
                $existingItem->quantity += $quantity;
                $existingItem->price += $itemTotalPrice;
                $existingItem->save();
            } else {
                // Item baru, buat OrderItem baru
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $itemTotalPrice,
                    'variant_details' => $jsonVariantDetails,
                    'temperature' => $item['temperature'] ?? 'Iced',
                    'sugar_level' => $item['sugar_level'] ?? 'Normal',
                    'ice_level' => $item['ice_level'] ?? 'Normal',
                ]);
            }

            // 5. Update Total Harga Order
            $order->total = OrderItem::where('order_id', $order->id)->sum('price');
            $order->save();

            DB::commit();

            // 6. Respon sukses dan kembalikan HTML keranjang yang diperbarui
            $productName = $product->name;

            // --- Perubahan dimulai di sini ---

            // Dapatkan order terbaru (yang baru diupdate) dengan relasi yang dibutuhkan
            $latestOrder = Order::where('user_id', Auth::id())
                ->where('status', 'pending')
                // Penting: Pastikan relasi items dan product dimuat
                ->with('items.product')
                ->latest()
                ->first();
            // Render view partial offcanvas dengan data terbaru
            $cartHtml = view('partials.offcanvas-cart-content', [
                // Gunakan name variabel yang sama dengan yang ada di View Composer Anda ($latestOrder)
                'latestOrder' => $latestOrder
            ])->render();

            // Kembalikan respons JSON yang berisi HTML keranjang yang baru
            return response()->json([
                'status' => 'success',
                'message' => "$quantity x $productName berhasil ditambahkan ke keranjang",
                'cartHtml' => $cartHtml, // Kunci baru yang berisi HTML
            ]);
            // --- Perubahan berakhir di sini ---


        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            // Mengembalikan status 500 dan pesan error
            return response()->json(['status' => 'error', 'message' => 'Gagal menambahkan ke keranjang, coba lagi. Detail: ' . $e->getMessage()], 500);
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
                $items = OrderItem::findOrFail($request->order_product_id);
                $product = Product::findOrFail($items->product_id);
                $order = Order::findOrFail($items->order_id);

                if ($order->user_id != Auth::user()->id) {
                    throw new \Exception('Akses Tidak Sah untuk pesanan ini.');
                }
                if ($order->status !== 'pending') {
                    throw new \Exception('Tidak dapat mengubah jumlah produk pada pesanan yang sudah selesai atau dibatalkan.');
                }

                if ($request->quantity > $product->stok) {
                    throw new \Exception("Maaf, hanya tersedia {$product->stok} barang untuk {$product->name}.");
                }

                // Ambil unit price dari item sebelum dikalikan quantity (ini asumsi, tapi diperlukan untuk kalkulasi ulang)
                // Kita bagi 'price' OrderItem (Total Item Price) dengan quantity lama untuk mendapatkan Unit Price
                $itemPrice = $items->price / $items->quantity;

                $newTotalPrice = $itemPrice * $request->quantity;

                $items->quantity = $request->quantity;
                $items->price = $newTotalPrice; // FIX: Menggunakan kolom 'price' untuk total harga item
                $items->save();

                // Hitung ulang total order dari semua item
                $order->total = OrderItem::where('order_id', $order->id)->sum('price'); // FIX: Menggunakan kolom 'total' dan sum('price')
                $order->save();
            });
            return redirect()->back()->with("success", 'Jumlah Produk berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function removeItem(Request $request)
    {
        try {
            $request->validate([
                'order_product_id' => 'required|exists:order_items,id',
            ]);

            $orderDeleted = false;
            $message = null;

            DB::transaction(function () use ($request, &$orderDeleted, &$message) {
                $items = OrderItem::findOrFail($request->order_product_id);
                $order = Order::findOrFail($items->order_id);
                $productName = Product::findOrFail($items->product_id)->name;

                if ($order->user_id !== Auth::id()) {
                    throw new \Exception('Akses tidak sah untuk pesanan ini.');
                }

                if ($order->status !== 'pending') {
                    throw new \Exception('Tidak dapat merubah pesanan yang telah selesai.');
                }

                $orderId = $order->id;

                // Hapus item
                $items->delete();

                // Hitung ulang total order dari semua item
                $order->total = OrderItem::where('order_id', $order->id)->sum('price'); // FIX: Menggunakan kolom 'total' dan sum('price')
                $order->save();


                $remainingCount = OrderItem::where('order_id', $orderId)->count();

                if ($remainingCount === 0) {
                    $order->delete();
                    $orderDeleted = true;
                    $message = 'Pesanan dihapus karena tidak ada produk di dalamnya.';
                } else {
                    $message = "Produk {$productName} berhasil di hapus dari pesanan.";
                }
            });

            if ($orderDeleted) {
                return redirect()->route('order.my')->with('info', 'Keranjang Anda kosong. ' . $message);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $errorMessage = $e->validator->errors()->first() ?? $e->getMessage();
            }
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    public function checkOut(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $order = Order::with('items.product')->findOrFail($request->order_id);

                if ($order->user_id != Auth::id()) {
                    return redirect()->route('orders.my')->with('error', 'Akses Tidak Sah untuk pesanan ini.');
                }

                if ($order->status !== 'pending') {
                    return redirect()->route('orders.detail', $order->id)->with('error', 'Pesanan ini sudah selesai');
                }

                if ($order->items->isEmpty()) {
                    return redirect()->route('orders.my')->with('error', 'Tidak dapat melakukan checkout pada pesanan yang kosong.');
                }

                $insufficientStock = [];
                foreach ($order->items as $item) {
                    $product = $item->product;

                    if ($item->quantity > $product->stok) {
                        $insufficientStock[] = "{$product->name} (diminta: {$item->quantity}, tersedia: {$product->stok})";
                    }
                }

                if (!empty($insufficientStock)) {
                    $productList = implode(', ', $insufficientStock);
                    return redirect()->route('orders.detail', $order->id)->with('error', "Stok tidak mencukupi untuk produk berikut: {$productList}");
                }

                // Kurangi stok
                foreach ($order->items as $item) {
                    $product = $item->product;
                    $product->stok -= $item->quantity;
                    $product->save();
                }

                // Update status order
                $order->status = 'completed';
                $order->save();

                return redirect()->route('orders.detail', $order->id)->with('success', 'Pembayaran Berhasil, terima kasih telah checkout!');
            });
        } catch (\Exception $e) {
            return redirect()->route('orders.my')->with('error', 'Terjadi Kesalahan saat checkout : ' . $e->getMessage());
        }
    }
}
