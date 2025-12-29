<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\BranchProduct;
use Illuminate\Http\Request;

class BranchProductController extends Controller
{
    public function index()
    {
        $items = BranchProduct::with(['branch','product'])->get();
        return view('admin.branch_product.index', compact('items'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('admin.branch_product.create', compact('branches','products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer|min:0',
            'price_override' => 'nullable|numeric',
            'status' => 'required|in:available,unavailable'
        ]);

        BranchProduct::create($data);
        return redirect()->route('branch-products.index')->with('success','Inventory ditambahkan');
    }

    public function edit(BranchProduct $branchProduct)
    {
        $branches = Branch::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('admin.branch_product.edit', compact('branchProduct','branches','products'));
    }

    public function update(Request $request, BranchProduct $branchProduct)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer|min:0',
            'price_override' => 'nullable|numeric',
            'status' => 'required|in:available,unavailable'
        ]);

        $branchProduct->update($data);
        return redirect()->route('branch-products.index')->with('success','Inventory diupdate');
    }

    public function destroy(BranchProduct $branchProduct)
    {
        $branchProduct->delete();
        return redirect()->route('branch-products.index')->with('success','Inventory dihapus');
    }
}
