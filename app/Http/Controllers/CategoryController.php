<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::latest()->get();
        return view('admin.category.index', compact('category'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'type' => 'required|string|max:255',
            ]);

            Category::create([
                'name' => $request->name,
                'type' => $request->type,
            ]);

            return redirect()
                ->route('admin.category.index')
                ->with('success', 'Data Telah Berhasil Ditambahkan');
        } catch (Exception $e) {
            Log::error('Store Category Error: '.$e->getMessage());
            return back()->with('error', 'Gagal menambah kategori, coba lagi.');
        }
    }

    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'type' => 'required|string|max:255|',
            ]);

            $category->update([
                'name' => $request->name,
                'type' => $request->type,
            ]);

            return redirect()
                ->route('admin.category.index')
                ->with('success', 'Data Telah Berhasil Diubah');
        } catch (Exception $e) {
            Log::error('Update Category Error: '.$e->getMessage());
            return back()->with('error', 'Gagal mengubah kategori, coba lagi.');
        }
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return redirect()
                ->route('admin.category.index')
                ->with('success', 'Data Telah Berhasil Dihapus');
        } catch (Exception $e) {
            Log::error('Delete Category Error: '.$e->getMessage());
            return back()->with('error', 'Gagal menghapus kategori, coba lagi.');
        }
    }
}
