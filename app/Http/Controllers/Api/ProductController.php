<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {

        $branchId = $request->branch_id ?? Branch::first()->id;

        $query = Product::with([
            'category',
            'variantTypes.options',
            'branchProducts'
        ]);

        $query->whereHas('branchProducts', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get()->map(function ($product) use ($branchId) {

            $branchProduct = $product->branchProducts
                ->firstWhere('branch_id', $branchId);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => (int) ($branchProduct->price_override ?? $product->price),
                'image' => $product->image
                    ? asset('storage/' . $product->image)
                    : null,

                'stock' => $branchProduct?->stock ?? 0,

                'category' => [
                    'id' => $product->category->id ?? null,
                    'name' => $product->category->name ?? null,
                ],

                'variant_types' => $product->variantTypes->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name,
                        'input_type' => $type->input_type,
                        'variant_options' => $type->options->map(function ($option) {
                            return [
                                'id' => $option->id,
                                'option_name' => $option->option_name,
                                'price_impact' => (int) $option->price_impact,
                            ];
                        })->values(),
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'branch_id' => $branchId,
            'data' => $products
        ]);
    }
}
