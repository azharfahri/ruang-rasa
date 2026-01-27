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
            ->latest()
            ->get();

        return view('kasir.orders.index', compact('orders'));
    }

    // Parameter $order dibuat opsional (?) agar bisa diakses tanpa ID saat awal
    public function create(Order $order = null)
    {
        // Jika tidak ada ID di URL, cari apakah ada order pending milik kasir ini
        if (!$order) {
            $order = Order::where('cashier_id', auth()->id())
                ->where('status', 'pending')
                ->where('total', 0) // Menandakan order baru yang belum diproses
                ->first();
        }

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

            // 1. CARI ATAU BUAT ORDER BARU
            $order = $order_id ? Order::find($order_id) : null;

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

            // 2. PROSES ITEM
            $branchProduct = BranchProduct::where('branch_id', $branchId)
                ->where('product_id', $request->product_id)
                ->where('status', 'available')
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

            // Cek jika item dengan varian yang sama sudah ada di keranjang
            $existingItem = $order->items()
                ->where('product_id', $request->product_id)
                ->get()
                ->first(function ($item) use ($variantIds) {
                    return $item->details->pluck('variant_option_id')->sort()->values()->toArray() === $variantIds;
                });

            $totalQty = ($existingItem ? $existingItem->quantity : 0) + $request->qty;

            if ($totalQty > $branchProduct->stock) {
                throw ValidationException::withMessages(['stock' => 'Stok tidak mencukupi']);
            }

            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $totalQty,
                    'subtotal' => $totalQty * $existingItem->price,
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

            $order->update(['total' => $order->items()->sum('subtotal')]);
            return $order;
        });

        // Redirect ke halaman create yang membawa ID Order
        return redirect()->route('cashier.orders.create', $order->id);
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

        return back();
    }

    public function payCash(Order $order)
    {
        abort_if($order->total <= 0, 400);
        abort_if($order->payment_status !== 'pending', 403);

        DB::transaction(function () use ($order) {
            $order->load('items');
            foreach ($order->items as $item) {
                $branchProduct = BranchProduct::where('branch_id', $order->branch_id)
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($branchProduct->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'stock' => "Stok {$item->product->name} tidak mencukupi",
                    ]);
                }

                $branchProduct->decrement('stock', $item->quantity);
                if ($branchProduct->stock <= 0) {
                    $branchProduct->update(['status' => 'soldout']);
                }
            }

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

        return redirect()->route('cashier.orders.index')->with('success', 'Pembayaran berhasil');
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

    public function history()
    {
        $orders = Order::where('branch_id', auth()->user()->branch_id)
            ->where('payment_status', 'settlement')
            ->latest()
            ->get();
        return view('kasir.orders.history', compact('orders'));
    }

    public function printReceipt(Order $order)
    {
        // Pastikan order sudah dibayar
        if ($order->payment_status !== 'settlement') {
            return back()->with('error', 'Struk hanya bisa dicetak untuk pesanan yang sudah lunas.');
        }

        $order->load(['items.product', 'items.details.variantOption']);
        return view('kasir.orders.print', compact('order'));
    }

    public function destroy(Order $order)
    {
        // Pastikan hanya bisa hapus yang statusnya pending
        if ($order->status !== 'pending') {
            return back()->with('error', 'Hanya order pending yang bisa dihapus.');
        }

        DB::transaction(function () use ($order) {
            // Hapus detail varian melalui item
            foreach ($order->items as $item) {
                $item->details()->delete();
            }
            // Hapus item
            $order->items()->delete();
            // Hapus order
            $order->delete();
        });

        return back()->with('success', 'Order berhasil dihapus.');
    }
}
