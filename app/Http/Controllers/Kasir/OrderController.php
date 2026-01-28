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
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('branch_id', auth()->user()->branch_id)
            ->with(['items.product', 'items.details.variantOption'])
            ->latest()
            ->get();

        return view('kasir.orders.index', compact('orders'));
    }

    public function create(Order $order = null)
    {
        $branchId = auth()->user()->branch_id;

        if (!$order || !$order->exists) {
            $order = Order::where('cashier_id', auth()->id())
                ->where('branch_id', $branchId)
                ->where('status', 'pending')
                ->latest()
                ->first();
        }

        if (!$order) {
            $order = new Order();
            $order->total = 0;
        }

        // Ambil SEMUA produk yang terdaftar di cabang ini (termasuk yang stok 0/soldout)
        $products = Product::with(['variantTypes.options', 'branchProducts' => function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])
            ->whereHas('branchProducts', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->get();

        return view('kasir.orders.create', compact('order', 'products'));
    }

    public function addItem(Request $request, $order_id = null)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
            'variants'   => 'nullable|array',
        ]);

        try {
            $order = DB::transaction(function () use ($request, $order_id) {
                $branchId = auth()->user()->branch_id;

                $branchProduct = BranchProduct::where('branch_id', $branchId)
                    ->where('product_id', $request->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($branchProduct->stock < $request->qty) {
                    throw new \Exception("Stok tidak mencukupi. Sisa: {$branchProduct->stock}");
                }

                // Cari atau Buat Order
                $order = $order_id
                    ? Order::where('id', $order_id)->where('branch_id', $branchId)->firstOrFail()
                    : Order::where('cashier_id', auth()->id())->where('status', 'pending')->latest()->first();

                if (!$order) {
                    $order = Order::create([
                        'branch_id'      => $branchId,
                        'cashier_id'     => auth()->id(),
                        'status'         => 'pending',
                        'payment_status' => 'pending',
                        'order_type'     => 'offline_dinein',
                        'total'          => 0,
                    ]);
                }

                // Hitung Harga & Cek Item Double (Logika Anda yang sudah ada tetap di sini)
                $basePrice = $branchProduct->price_override ?? $branchProduct->product->price;
                $variantPrice = 0;
                $variantIds = [];

                if ($request->variants) {
                    $optionIds = collect($request->variants)->flatten()->toArray();
                    $options = VariantOption::whereIn('id', $optionIds)->get();
                    foreach ($options as $option) {
                        $variantPrice += $option->price_impact;
                        $variantIds[] = $option->id;
                    }
                }
                sort($variantIds);
                $finalPrice = $basePrice + $variantPrice;

                $existingItem = $order->items()->where('product_id', $request->product_id)->get()
                    ->first(function ($item) use ($variantIds) {
                        return $item->details->pluck('variant_option_id')->sort()->values()->toArray() === $variantIds;
                    });

                if ($existingItem) {
                    $newQty = $existingItem->quantity + $request->qty;
                    $existingItem->update([
                        'quantity' => $newQty,
                        'subtotal' => $newQty * $existingItem->price,
                    ]);
                } else {
                    $item = OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $request->product_id,
                        'quantity'   => $request->qty,
                        'price'      => $finalPrice,
                        'subtotal'   => $finalPrice * $request->qty,
                    ]);

                    if ($request->variants) {
                        foreach ($options as $opt) {
                            $item->details()->create([
                                'variant_type_id'   => $opt->variant_type_id,
                                'variant_option_id' => $opt->id,
                                'price_impact'      => $opt->price_impact,
                            ]);
                        }
                    }
                }

                $order->update(['total' => $order->items()->sum('subtotal')]);
                return $order;
            });

            // --- BAGIAN MODIFIKASI AJAX ---
            if ($request->ajax()) {
                // Load ulang relation agar data terbaru muncul di keranjang
                $order->load(['items.product', 'items.details.variantOption']);
                return view('kasir.orders.partials.cart', compact('order'))->render();
            }

            return redirect()->route('cashier.orders.create', $order->id)->with('success', 'Item ditambahkan');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['stock' => $e->getMessage()]);
        }
    }

    public function minusItem(Order $order, OrderItem $item)
    {
        abort_if($item->order_id !== $order->id, 403);

        DB::transaction(function () use ($order, $item) {
            if ($item->quantity > 1) {
                $item->decrement('quantity');
                $item->update(['subtotal' => $item->quantity * $item->price]);
            } else {
                $item->details()->delete();
                $item->delete();
            }
            $order->update(['total' => $order->items()->sum('subtotal')]);
        });

        if (request()->ajax()) {
            $order->load(['items.product', 'items.details.variantOption']);
            return view('kasir.orders.partials.cart', compact('order'))->render();
        }
        return back();
    }

    public function updateVariant(Request $request, Order $order, OrderItem $item)
    {
        DB::transaction(function () use ($request, $order, $item) {
            $item->details()->delete();

            $branchProduct = BranchProduct::where('branch_id', $order->branch_id)
                ->where('product_id', $item->product_id)
                ->firstOrFail();

            $basePrice = $branchProduct->price_override ?? $branchProduct->product->price;
            $variantPrice = 0;

            if ($request->variants) {
                $optionIds = collect($request->variants)->flatten()->toArray();
                $options = VariantOption::whereIn('id', $optionIds)->get();

                foreach ($options as $option) {
                    $variantPrice += $option->price_impact;
                    $item->details()->create([
                        'variant_type_id'   => $option->variant_type_id,
                        'variant_option_id' => $option->id,
                        'price_impact'      => $option->price_impact,
                    ]);
                }
            }

            $newPrice = $basePrice + $variantPrice;
            $item->update([
                'price' => $newPrice,
                'subtotal' => $newPrice * $item->quantity
            ]);

            $order->update(['total' => $order->items()->sum('subtotal')]);
        });

        if ($request->ajax()) {
            $order->load(['items.product', 'items.details.variantOption']);
            return view('kasir.orders.partials.cart', compact('order'))->render();
        }
        return back();
    }

    public function payCash(Request $request, Order $order) // Tambahkan Request di sini
    {
        // Validasi minimal nama customer harus diisi
        $request->validate([
            'customer_name' => 'required|string|max:255',
        ]);

        if ($order->total <= 0) return back()->with('error', 'Keranjang kosong');

        try {
            DB::transaction(function () use ($order, $request) { // Masukkan $request ke dalam closure
                foreach ($order->items as $item) {
                    $bp = BranchProduct::where('branch_id', $order->branch_id)
                        ->where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($bp->stock < $item->quantity) {
                        throw new \Exception("Stok produk {$item->product->name} tiba-tiba habis!");
                    }

                    $bp->decrement('stock', $item->quantity);
                    if ($bp->stock <= 0) {
                        $bp->update(['status' => 'soldout']);
                    }
                }

                Transaction::create([
                    'order_id' => $order->id,
                    'payment_gateway' => 'cash',
                    'payment_method' => 'cash',
                    'amount' => $order->total,
                    'status' => 'paid',
                ]);

                // Update status, payment_status, dan simpan customer_name
                $order->update([
                    'customer_name' => $request->customer_name,
                    'status' => 'processing',
                    'payment_status' => 'settlement'
                ]);
            });

            // Jika ingin langsung cetak struk, bisa ganti redirect ke halaman receipt
            return redirect()->route('cashier.orders.index')->with('success', 'Pembayaran Berhasil!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markReady(Order $order)
    {
        $order->update(['status' => 'ready']);
        return back()->with('success', 'Pesanan siap diambil');
    }

    public function markCompleted(Order $order)
    {
        $order->update(['status' => 'completed']);
        return back()->with('success', 'Pesanan selesai');
    }

    public function printReceipt(Order $order)
    {
        $order->load(['items.product', 'items.details.variantOption']);
        return view('kasir.orders.print', compact('order'));
    }

    public function history()
    {
        $orders = Order::where('branch_id', auth()->user()->branch_id)
            ->where('payment_status', 'settlement')
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        return view('kasir.orders.history', compact('orders'));
    }

    public function destroy(Order $order)
    {
        // Pastikan hanya bisa hapus order yang masih pending
        if ($order->status !== 'pending') return back()->with('error', 'Tidak bisa menghapus order yang sudah dibayar');

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $item->details()->delete();
            }
            $order->items()->delete();
            $order->delete();
        });

        return redirect()->route('cashier.orders.index')->with('success', 'Order berhasil dibatalkan');
    }
}
