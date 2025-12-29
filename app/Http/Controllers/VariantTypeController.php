<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\VariantType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VariantTypeController extends Controller
{
    public function index(Product $product)
    {
        $variantTypes = $product->variantTypes()->withCount('options')->get();
        return view('admin.variant_type.index', compact('product', 'variantTypes'));
    }

    public function create(Product $product)
    {
        return view('admin.variant_type.create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => [
                'required',
                Rule::unique('variant_types')
                    ->where(fn ($q) => $q->where('product_id', $product->id)),
            ],
            'input_type' => 'required|in:radio,checkbox',
        ]);

        $product->variantTypes()->create($data);

        return redirect()->route('product.variant-types.index', $product);
    }

    public function edit(Product $product, VariantType $variantType)
    {
        return view('admin.variant_type.edit', compact('product', 'variantType'));
    }

    public function update(Request $request, Product $product, VariantType $variantType)
    {
        $data = $request->validate([
            'name' => [
                'required',
                Rule::unique('variant_types')
                    ->where(fn ($q) => $q->where('product_id', $product->id))
                    ->ignore($variantType->id),
            ],
            'input_type' => 'required|in:radio,checkbox',
        ]);

        $variantType->update($data);

        return redirect()->route('product.variant-types.index', $product);
    }

    public function destroy(Product $product, VariantType $variantType)
    {
        $variantType->delete();

        return redirect()->route('product.variant-types.index', $product);
    }
}
