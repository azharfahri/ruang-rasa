<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::select('id', 'name', 'slug')->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data Kategori',
            'data'    => $categories
        ], 200);
    }

    public function show($slug)
    {
        try {
            $category = Category::where('slug', $slug)
                ->with(['products' => function ($query) {
                    $query->with('varianttypes.options');
                }])
                ->firstOrFail();

            return response()->json(['success' => true, 'data' => $category]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
