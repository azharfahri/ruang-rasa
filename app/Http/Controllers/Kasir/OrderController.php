<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\VariantOption;
use App\Models\BranchProduct;
use App\Models\Category;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\RefundTransaction;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        $categories = Category::all(); // Pastikan model Category sudah ada
        $products = Product::with(['branchProducts', 'variantTypes.options'])->get();
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

        return view('kasir.orders.create', compact('order', 'products', 'categories'));
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
                    // Generate kode unik 5 karakter, misal: RR-A1Z
                    $pickupCode = 'RR-' . strtoupper(Str::random(3));

                    // Pastikan kodenya belum pernah dipakai hari ini (opsional tapi aman)
                    while (Order::where('pickup_code', $pickupCode)->whereDate('created_at', today())->exists()) {
                        $pickupCode = 'RR-' . strtoupper(Str::random(3));
                    }
                    $order = Order::create([
                        'branch_id'      => $branchId,
                        'cashier_id'     => auth()->id(),
                        'status'         => 'pending',
                        'payment_status' => 'pending',
                        'order_type'     => 'offline_dinein',
                        'total'          => 0,
                        'pickup_code'    => $pickupCode,
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

    public function payCash(Request $request, Order $order)
    {
        // 1. Validasi: Nama wajib, dan Uang Tunai tidak boleh kurang dari Total
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'cash_received' => 'required|numeric|min:' . $order->total,
        ]);

        if ($order->total <= 0) return back()->with('error', 'Keranjang kosong');

        try {
            DB::transaction(function () use ($order, $request) {
                // 2. Potong Stok (Logika yang sudah kamu punya)
                foreach ($order->items as $item) {
                    $bp = BranchProduct::where('branch_id', $order->branch_id)
                        ->where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($bp->stock < $item->quantity) {
                        throw new \Exception("Stok produk {$item->product->name} tidak mencukupi!");
                    }
                    $bp->decrement('stock', $item->quantity);
                }

                // 3. Simpan ke Tabel Transactions (Gunakan kolom baru)
                Transaction::create([
                    'order_id' => $order->id,
                    'payment_gateway' => 'cash',
                    'payment_method' => 'cash',
                    'amount' => $order->total,
                    'cash_received' => $request->cash_received,
                    'change_amount' => $request->cash_received - $order->total, // Hitung otomatis
                    'status' => 'paid',
                ]);

                // 4. Update Order
                $order->update([
                    'customer_name' => $request->customer_name,
                    'status' => 'processing',
                    'payment_status' => 'settlement'
                ]);
            });

            $kembalian = number_format($request->cash_received - $order->total, 0, ',', '.');
            return redirect()->route('cashier.orders.index')->with('success', "Pembayaran Berhasil! Kembalian: Rp $kembalian");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function payMidtrans(Request $request, Order $order)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
        ]);

        if ($order->total <= 0) {
            return response()->json([
                'message' => 'Keranjang masih kosong'
            ], 422);
        }

        // CONFIG MIDTRANS (WAJIB)
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'ORD-' . $order->id . '-' . time();

        $enabledPayments = [
            'gopay',
            'shopeepay',
            'dana',
            'ovo',
            'bank_transfer',
        ];

        $item_details = [];
        foreach ($order->items as $item) {
            $item_details[] = [
                'id'       => $item->product_id,
                'price'    => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name'     => substr($item->product->name, 0, 50),
            ];
        }


        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $order->total,
            ],
            'item_details' => $item_details,
            'customer_details' => [
                'first_name' => $request->customer_name,
            ],
            'enabled_payments' => $enabledPayments,
            'bank_transfer' => [
                'bank' => ['bca', 'bni', 'bri', 'mandiri']
            ],
        ];



        try {
            $snapToken = Snap::getSnapToken($params);

            $order->update([
                'status' => 'pending',
                'payment_type' => 'midtrans',
                'payment_ref' => $orderId,
                'customer_name' => $request->customer_name,
            ]);

            // PERBAIKAN: Kirim order_id agar dibaca Javascript
            return response()->json([
                'snap_token' => $snapToken,
                'order_id'   => $order->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function paymentSuccess(Request $request, Order $order)
    {
        if ($order->status === 'processing' || $order->payment_status === 'settlement') {
            return response()->json(['status' => 'already_processed']);
        }

        try {
            $result = $request->input('result');

            // Logika penentuan label Payment Method dinamis
            $method = 'Midtrans';
            if (isset($result['payment_type'])) {
                if ($result['payment_type'] == 'bank_transfer' && isset($result['va_numbers'][0]['bank'])) {
                    $method = strtoupper($result['va_numbers'][0]['bank']); // Contoh: BCA
                } elseif ($result['payment_type'] == 'cstore') {
                    $method = ucfirst($result['store'] ?? 'Retail'); // Indomaret/Alfamart
                } else {
                    $method = ucfirst(str_replace('_', ' ', $result['payment_type'])); // Gopay, Qris, dll
                }
            }

            DB::transaction(function () use ($order, $method, $result) {
                // 1. Potong Stok
                foreach ($order->items as $item) {
                    $bp = BranchProduct::where('branch_id', $order->branch_id)
                        ->where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($bp->stock < $item->quantity) {
                        throw new \Exception("Stok {$item->product->name} tidak cukup");
                    }
                    $bp->decrement('stock', $item->quantity);
                }

                // 2. Simpan ke Tabel Transactions (Sesuai database kamu)
                Transaction::create([
                    'order_id'               => $order->id,
                    'payment_gateway'        => 'Midtrans',
                    'payment_method'         => $method,
                    'amount'                 => $order->total,
                    'cash_received'          => $order->total, // Non-tunai dianggap lunas
                    'change_amount'          => 0,
                    'gateway_transaction_id' => $result['transaction_id'] ?? null, // Simpan ID dari Midtrans
                    'status'                 => 'paid',
                ]);

                // 3. Update Order
                $order->update([
                    'status'         => 'processing',
                    'payment_status' => 'settlement'
                ]);
            });

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function midtransNotification(Request $request)
    {
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');

        try {
            $notif = new Notification();
            $transactionStatus = $notif->transaction_status;
            $orderIdFull = $notif->order_id; // ORD-ID-TIME
            $orderIdParts = explode('-', $orderIdFull);
            $order = Order::find($orderIdParts[1]);

            if (!$order) return response()->json(['message' => 'Order not found'], 404);
            if ($order->payment_status === 'settlement') return response()->json(['message' => 'Lunas']);

            if (in_array($transactionStatus, ['settlement', 'capture'])) {

                // Penentuan method untuk Webhook
                $method = ucfirst(str_replace('_', ' ', $notif->payment_type));
                if ($notif->payment_type == 'bank_transfer' && isset($notif->va_numbers[0]->bank)) {
                    $method = strtoupper($notif->va_numbers[0]->bank);
                }

                DB::transaction(function () use ($order, $method, $notif) {
                    // Potong Stok
                    foreach ($order->items as $item) {
                        $bp = BranchProduct::where('branch_id', $order->branch_id)
                            ->where('product_id', $item->product_id)
                            ->lockForUpdate()->first();
                        if ($bp && $bp->stock >= $item->quantity) {
                            $bp->decrement('stock', $item->quantity);
                        }
                    }

                    // Simpan Transaksi (Gunakan updateOrCreate untuk cegah duplikasi dengan paymentSuccess)
                    Transaction::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'payment_gateway'        => 'Midtrans',
                            'payment_method'         => $method,
                            'amount'                 => $order->total,
                            'cash_received'          => $order->total,
                            'change_amount'          => 0,
                            'gateway_transaction_id' => $notif->transaction_id,
                            'status'                 => 'paid',
                        ]
                    );

                    $order->update([
                        'status' => 'processing',
                        'payment_status' => 'settlement'
                    ]);
                });
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
        $order->load(['items.product', 'items.refundItems', 'items.details.variantOption']);
        return view('kasir.orders.print', compact('order'));
    }

    public function destroy(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Tidak bisa membatalkan order ini ');
        }

        DB::transaction(function () use ($order) {

            foreach ($order->items as $item) {
                $branchProduct = BranchProduct::where('branch_id', $order->branch_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($branchProduct) {
                    $branchProduct->increment('stock', $item->quantity);
                }
            }

            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'cancel'
            ]);
        });

        return back()->with('success', 'Order berhasil dibatalkan');
    }
}
