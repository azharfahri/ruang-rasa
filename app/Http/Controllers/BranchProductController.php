<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\BranchProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchProductController extends Controller
{
    // HALAMAN 1: LIST CABANG
    public function index()
    {
        $branches = Branch::withCount('branchProducts')->get();
        return view('admin.branch_product.branches', compact('branches'));
    }

    // HALAMAN 2: INVENTORY PER CABANG
    public function show(Branch $branch)
    {
        $items = BranchProduct::with('product')
            ->where('branch_id', $branch->id)
            ->get();

        $products = Product::whereNotIn(
            'id',
            $items->pluck('product_id')
        )->orderBy('name')->get();

        return view('admin.branch_product.index', compact('branch', 'items', 'products'));
    }

    // public function create(Branch $branch)
    // {
    //     $products = Product::orderBy('name')->get();
    //     return view('admin.branch_product.create', compact('branch', 'products'));
    // }

    public function store(Request $request)
    {
        // 1. Validasi awal untuk memastikan input ada
        if (!$request->has('products')) {
            return back()->with('error', 'Tidak ada produk yang dipilih.');
        }

        // 2. Ambil branch_id dari input hidden yang ada di modal
        $branchId = $request->branch_id;

        // 3. Looping data products yang dikirim dari modal
        foreach ($request->products as $productId => $item) {

            // Hanya proses jika checkbox 'selected' dicentang
            if (isset($item['selected'])) {

                $stock = $item['stock'] ?? 0;

                // Simpan menggunakan Model BranchProduct (Cara yang kamu inginkan)
                BranchProduct::create([
                    'branch_id'      => $branchId,
                    'product_id'     => $productId,
                    'stock'          => $stock,
                    'price_override' => $item['price_override'] ?: null, // Simpan null jika kosong
                    'status'         => $stock == 0 ? 'soldout' : 'available',
                ]);
            }
        }

        return redirect()
            ->route('branch-products.show', $branchId)
            ->with('success', 'Produk berhasil ditambahkan ke cabang');
    }

    public function edit(BranchProduct $branchProduct)
    {
        $products = Product::orderBy('name')->get();
        return view('admin.branch_product.edit', compact('branchProduct', 'products'));
    }

    public function update(Request $request, BranchProduct $branchProduct)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer|min:0',
            'price_override' => 'nullable|numeric',
            'status' => 'required|in:available,soldout'
        ]);

        $branchProduct->update($data);

        return redirect()
            ->route('branch-products.show', $branchProduct->branch_id)
            ->with('success', 'Inventory diupdate');
    }

    public function kasirIndex(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $search = $request->get('search');
        $status = $request->get('status');

        $query = BranchProduct::with('product')
            ->where('branch_id', $branchId);

        // Filter Search (Nama Produk)
        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        // Filter Status
        if ($status == 'available') {
            $query->where('stock', '>', 0);
        } elseif ($status == 'soldout') {
            $query->where('stock', 0);
        }

        $items = $query->orderBy('stock', 'asc') // Menampilkan stok tersedikit di atas agar terpantau
            ->paginate(10)
            ->withQueryString();

        return view('kasir.penyimpanan.index', compact('items'));
    }

    public function adjustStock(Request $request, BranchProduct $branchProduct)
    {
        abort_if(
            $branchProduct->branch_id !== auth()->user()->branch_id,
            403
        );

        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($branchProduct, $request) {

            $branchProduct->update([
                'stock'  => $request->stock,
                'status' => $request->stock > 0 ? 'available' : 'soldout',
            ]);
        });

        return back()->with('success', 'Stok berhasil diperbarui');
    }


    public function destroy(BranchProduct $branchProduct)
    {
        $branchProduct->delete();
        return redirect()
            ->route('branch-products.show', $branchProduct->branch_id)
            ->with('success', 'Inventory dihapus');
    }
}
