<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Untuk Pengelolaan File Gambar
use Exception;

class ProductController extends Controller
{
    /**
     * Tampilkan semua produk.
     */
    public function index()
    {
        try {
            // Eager loading relasi 'category' agar efisien
            $product = Product::with('category')->orderBy('id', 'desc')->get();
            return view('admin.product.index', compact('product'));
        } catch (Exception $e) {
            Log::error('Index Product Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menampilkan data produk.');
        }
    }

    /**
     * Form tambah produk.
     */
    public function create()
    {
        try {
            $categories = Category::all();
            return view('admin.product.create', compact('categories'));
        } catch (Exception $e) {
            Log::error('Create Product Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuka form tambah produk.');
        }
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255|unique:products,name',
            'description'   => 'required|string',
            'price'         => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'category_id'   => 'required|exists:categories,id',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $gambarPath = null;
            if ($request->hasFile('image')) {
                // Simpan gambar di storage/app/public/products
                $gambarPath = $request->file('image')->store('products', 'public');
            }

            // CREATE PRODUK UTAMA
            Product::create([
                'name'          => $request->name,
                'slug'          => Str::slug($request->name),
                'description'   => $request->description,
                'price'         => $request->price,
                'stock'         => $request->stock,
                'category_id'   => $request->category_id,
                'image'         => $gambarPath,
            ]);

            return redirect()->route('admin.product.index')
                ->with('success', 'Data produk berhasil ditambahkan.');
        } catch (Exception $e) {
            Log::error('Store Product Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan produk.');
        }
    }

    /**
     * Tampilkan detail produk (Dibiarkan kosong).
     */
    public function show(Product $product)
    {
        // ...
    }

    /**
     * Form edit produk.
     */
    public function edit(Product $product)
    {
        try {
            $categories = Category::all();
            return view('admin.product.edit', compact('product', 'categories'));
        } catch (Exception $e) {
            Log::error('Edit Product Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuka form edit produk.');
        }
    }

    /**
     * Update produk.
     */
    public function update(Request $request, Product $product)
    {
        // Validasi Produk
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // VALIDASI VARIANT TYPES
            'variant_types' => 'nullable|array',
            'variant_types.*.id' => 'nullable|integer',
            'variant_types.*.name' => 'required|string',
            'variant_types.*.input_type' => 'required|string|in:radio,checkbox',

            // VALIDASI VARIANT OPTIONS
            'variant_types.*.options' => 'nullable|array',
            'variant_types.*.options.*.id' => 'nullable|integer',
            'variant_types.*.options.*.option_name' => 'required|string',
            'variant_types.*.options.*.price_impact' => 'required|numeric',
        ]);

        // ===========================
        // 1. UPDATE PRODUK
        // ===========================

        $data = $request->only(['name', 'description', 'price', 'stock', 'category_id']);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        // ===========================
        // 2. UPDATE VARIANT TYPES
        // ===========================

        $typeIdsToKeep = [];

        if ($request->variant_types) {
            foreach ($request->variant_types as $vt) {
                $variantType = $product->variantTypes()->updateOrCreate(
                    ['id' => $vt['id'] ?? null],
                    [
                        'name' => $vt['name'],
                        'input_type' => $vt['input_type'],
                    ]
                );

                $typeIdsToKeep[] = $variantType->id;

                // ===========================
                // 3. UPDATE VARIANT OPTIONS
                // ===========================

                $optionIdsToKeep = [];

                if (!empty($vt['options'])) {
                    foreach ($vt['options'] as $opt) {
                        $option = $variantType->variantOptions()->updateOrCreate(
                            ['id' => $opt['id'] ?? null],
                            [
                                'option_name' => $opt['option_name'],
                                'price_impact' => $opt['price_impact'],
                            ]
                        );
                        $optionIdsToKeep[] = $option->id;
                    }
                }

                // Hapus opsi yang tidak ada lagi
                $variantType->variantOptions()
                    ->whereNotIn('id', $optionIdsToKeep)
                    ->delete();
            }
        }

        // Hapus varian type yang tidak ada lagi
        $product->variantTypes()->whereNotIn('id', $typeIdsToKeep)->delete();

        return redirect()->route('admin.product.index')
            ->with('success', 'Produk dan varian berhasil diperbarui!');
    }



    /**
     * Hapus produk.
     * Metode getVariants telah dihapus karena tidak ada varian.
     */
    public function destroy(Product $product)
    {
        try {
            // Hapus gambar jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return redirect()->route('admin.product.index')
                ->with('success', 'Produk berhasil dihapus.');
        } catch (Exception $e) {
            Log::error('Destroy Product Error: ' . $e->getMessage());
            // Tangani jika produk tidak bisa dihapus karena relasi lain
            $errorMessage = Str::contains($e->getMessage(), 'Foreign key')
                ? 'Gagal menghapus produk. Pastikan produk ini tidak terkait dengan data transaksi (Order/Cart).'
                : 'Gagal menghapus produk.';

            return back()->with('error', $errorMessage);
        }
    }
}
