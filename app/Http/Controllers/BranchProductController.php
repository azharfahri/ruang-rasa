<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\BranchProduct;
use Illuminate\Http\Request;

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

        return view('admin.branch_product.index', compact('branch', 'items'));
    }

    public function create(Branch $branch)
    {
        $products = Product::orderBy('name')->get();
        return view('admin.branch_product.create', compact('branch', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer|min:0',
            'price_override' => 'nullable|integer|min:0',
        ]);

        $data['status'] = $data['stock'] == 0
            ? 'soldout'
            : 'available';

        BranchProduct::create($data);


        return redirect()
            ->route('branch-products.show', $data['branch_id'])
            ->with('success', 'Produk berhasil ditambahkan');
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

    public function destroy(BranchProduct $branchProduct)
    {
        $branchProduct->delete();
        return redirect()
            ->route('branch-products.show', $branchProduct->branch_id)
            ->with('success', 'Inventory dihapus');
    }
}
