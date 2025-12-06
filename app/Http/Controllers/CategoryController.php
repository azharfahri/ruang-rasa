<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori terbaru (Read All).
     */
    public function index()
    {
        try {
            // Mengambil semua data kategori, diurutkan berdasarkan created_at.
            $categories = Category::latest()->get();

            // Menggunakan $categories untuk konsistensi
            return view('admin.category.index', compact('categories'));
        } catch (Exception $e) {
            Log::error('Index Category Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data kategori.');
        }
    }

    public function create()
    {
        // Mendefinisikan nilai ENUM yang mungkin untuk 'type' di sini (jika perlu)
        // atau ambil dari konstanta di Model jika ada.
        $categoryTypes = ['food', 'drink', 'dessert', 'addon', 'other'];
        return view('admin.category.create', compact('categoryTypes'));
    }

    /**
     * Simpan kategori baru ke database (Create).
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'type' => 'required|in:food,drink,dessert,addon,other',
            ]);

            // Tambahkan Slug secara manual sebelum penyimpanan
            $validatedData['slug'] = Str::slug($request->name);

            // Gunakan Eloquent: Create
            Category::create($validatedData);

            return redirect()
                ->route('admin.category.index')
                ->with('success', 'Data Telah Berhasil Ditambahkan');
        } catch (Exception $e) {
            Log::error('Store Category Error: ' . $e->getMessage());
            // Periksa apakah ini Error Validasi (misalnya 'type' salah)
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return back()->withErrors($e->errors())->withInput();
            }
            return back()->with('error', 'Gagal menambah kategori, coba lagi.');
        }
    }

    /**
     * Tampilkan form edit (menggunakan Route Model Binding).
     */
    public function edit(Category $category)
    {
        // Model Category sudah otomatis di-resolve oleh Laravel (Route Model Binding)
        $categoryTypes = ['food', 'drink', 'dessert', 'addon', 'other'];
        return view('admin.category.edit', compact('category', 'categoryTypes'));
    }

    /**
     * Update kategori yang sudah ada (Update).
     */
    public function update(Request $request, Category $category)
    {
        try {
            // Menggunakan Route Model Binding, $category sudah tersedia
            $validatedData = $request->validate([
                // Validasi unique: mengecualikan ID Model saat ini
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'type' => 'required|in:food,drink,dessert,addon,other',
            ]);

            // Tambahkan Slug baru
            $validatedData['slug'] = Str::slug($request->name);

            // Gunakan Eloquent: Update
            $category->update($validatedData);

            return redirect()
                ->route('admin.category.index')
                ->with('success', 'Data Telah Berhasil Diubah');
        } catch (Exception $e) {
            Log::error('Update Category Error: ' . $e->getMessage());
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return back()->withErrors($e->errors())->withInput();
            }
            return back()->with('error', 'Gagal mengubah kategori, coba lagi.');
        }
    }

    /**
     * Hapus kategori dari database (Delete).
     */
    public function destroy(Category $category)
    {
        try {
            // Gunakan Eloquent: Delete
            $category->delete();

            return redirect()
                ->route('admin.category.index')
                ->with('success', 'Data Telah Berhasil Dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error Foreign Key Constraint
            Log::error('Delete Category FK Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus kategori. Pastikan tidak ada produk yang menggunakan kategori ini.');
        } catch (Exception $e) {
            Log::error('Delete Category Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus kategori, coba lagi.');
        }
    }
}
