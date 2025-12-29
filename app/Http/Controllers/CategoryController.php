<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        $categoryTypes = ['food', 'drink', 'dessert', 'addon', 'other'];
        return view('admin.category.create', compact('categoryTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'type' => 'required|in:food,drink,dessert,addon,other',
        ]);

        $data['slug'] = Str::slug($data['name']);

        Category::create($data);

        return redirect()
            ->route('category.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit(Category $category)
    {
        $categoryTypes = ['food', 'drink', 'dessert', 'addon', 'other'];
        return view('admin.category.edit', compact('category', 'categoryTypes'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'type' => 'required|in:food,drink,dessert,addon,other',
        ]);

        $data['slug'] = Str::slug($data['name']);

        $category->update($data);

        return redirect()
            ->route('category.index')
            ->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('category.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
