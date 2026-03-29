<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::with('category');

        if (request()->category_id) {
            $query->where('category_id', request()->category_id);
        }

        if (request()->search) {
            $query->where('name', 'like', '%' . request()->search . '%');
        }

        $products = $query->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => (int) $product->price,
                'image' => $product->image
                    ? asset('storage/' . $product->image)
                    : null,
                'category' => [
                    'id' => $product->category->id ?? null,
                    'name' => $product->category->name ?? null,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
