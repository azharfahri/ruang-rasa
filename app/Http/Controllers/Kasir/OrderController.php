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
            ->with(['items.product'])
            ->latest()
            ->get();

        return view('kasir.orders.index', compact('orders'));
    }
    public function create(Order $order = null)
    {
        // Jika tidak ada ID di URL, cari order pending terbaru milik kasir ini
        if (!$order || !$order->exists) {
            $order = Order::where('cashier_id', auth()->id())
                ->where('status', 'pending')
                ->latest() // Ambil yang paling baru dibuat
                ->first();
        }

        // Jika benar-benar tidak ada order pending, buat object kosong agar view tidak error
        if (!$order) {
            $order = new Order();
            $order->total = 0;
        }

        // Load data produk (kode stock, dll tetap sama)
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

    public function addItem(Request $request, $order_id = null)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
            'variants'   => 'nullable|array',
        ]);

        $order = DB::transaction(function () use ($request, $order_id) {
            $branchId = auth()->user()->branch_id;

            // Cari order berdasarkan ID yang dikirim,
            // ATAU cari order pending milik kasir yang sudah ada
            $order = $order_id ? Order::find($order_id) : Order::where('cashier_id', auth()->id())
                ->where('status', 'pending')
                ->latest()
                ->first();

            // Jika benar-benar tidak ada order pending sama sekali, baru buat baru
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

            // --- SISA KODE (pencarian produk, hitung varian, dll) TETAP SAMA ---
            // ... (kode existing item, create item, update total tetap sama seperti milikmu)

            $branchProduct = BranchProduct::where('branch_id', $branchId)
                ->where('product_id', $request->product_id)
                ->lockForUpdate()
                ->firstOrFail();

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

                foreach ($variantIds as $id) {
                    $opt = VariantOption::find($id);
                    $item->details()->create([
                        'variant_type_id'   => $opt->variant_type_id,
                        'variant_option_id' => $opt->id,
                        'price_impact'      => $opt->price_impact,
                    ]);
                }
            }

            $order->update(['total' => $order->items()->sum('subtotal')]);
            return $order;
        });

        return redirect()->route('cashier.orders.create', $order->id);
    }

    // FITUR MINUS ITEM (PENTING!)
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

        return back();
    }

    // FITUR UPDATE VARIANT (PENTING!)
    public function updateVariant(Request $request, Order $order, OrderItem $item)
    {
        DB::transaction(function () use ($request, $order, $item) {
            // 1. Hapus detail lama
            $item->details()->delete();

            // 2. Hitung harga baru
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

            // 3. Update harga item
            $newPrice = $basePrice + $variantPrice;
            $item->update([
                'price' => $newPrice,
                'subtotal' => $newPrice * $item->quantity
            ]);

            // 4. Update total order
            $order->update(['total' => $order->items()->sum('subtotal')]);
        });

        return back()->with('success', 'Varian berhasil diperbarui');
    }

    public function payCash(Order $order)
    {
        if ($order->total <= 0) return back()->with('error', 'Keranjang kosong');

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $bp = BranchProduct::where('branch_id', $order->branch_id)
                    ->where('product_id', $item->product_id)->lockForUpdate()->firstOrFail();

                if ($bp->stock < $item->quantity) throw ValidationException::withMessages(['stock' => 'Stok tidak cukup']);

                $bp->decrement('stock', $item->quantity);
                if ($bp->stock <= 0) $bp->update(['status' => 'soldout']);
            }

            Transaction::create([
                'order_id' => $order->id,
                'payment_gateway' => 'cash',
                'payment_method' => 'cash',
                'amount' => $order->total,
                'status' => 'paid',
            ]);

            $order->update(['status' => 'processing', 'payment_status' => 'settlement']);
        });

        return redirect()->route('cashier.orders.index')->with('success', 'Pembayaran berhasil');
    }

    // Fungsi tambahan lainnya (ready, complete, history, printReceipt, destroy) tetap sama seperti sebelumnya...
    public function markReady(Order $order)
    {
        $order->update(['status' => 'ready']);
        return back();
    }
    public function markCompleted(Order $order)
    {
        $order->update(['status' => 'completed']);
        return back();
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
            ->latest()
            ->get();

        return view('kasir.orders.history', compact('orders'));
    }
    
    public function destroy(Order $order)
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $item->details()->delete();
            }
            $order->items()->delete();
            $order->delete();
        });
        return redirect()->route('cashier.orders.index')->with('success', 'Order dihapus');
    }
}
