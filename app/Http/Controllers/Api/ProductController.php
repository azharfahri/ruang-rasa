<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variantTypes.options']);

        if ($request->branch_id) {
            $query->whereHas('branchProducts', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => (int) $product->price,
                'image' => $product->image ? asset('storage/' . $product->image) : null,
                'category' => [
                    'id' => $product->category->id ?? null,
                    'name' => $product->category->name ?? null,
                ],
                // TAMBAHKAN DATA VARIAN KE DALAM RESPONSE JSON
                'variant_types' => $product->variantTypes->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name,
                        'input_type' => $type->input_type, // radio atau checkbox
                        'variant_options' => $type->options->map(function ($option) {
                            return [
                                'id' => $option->id,
                                'option_name' => $option->option_name,
                                'price_impact' => (int) $option->price_impact,
                            ];
                        }),
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
