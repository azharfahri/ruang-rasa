<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant; // <<< PASTIKAN MODEL INI ADA
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // <<< IMPORT DB UNTUK TRANSAKSI
use Illuminate\Support\Facades\Storage; // <<< IMPORT STORAGE

class ProductController extends Controller
{
    /**
     * Tampilkan semua produk.
     */
    public function index()
    {
        try {
            $product = Product::with('category')->orderBy('id', 'desc')->get();
            return view('admin.product.index', compact('product'));
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
            // Validasi Produk Utama
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            // Validasi Varian Baru (jika ada)
            'variants' => 'nullable|array',
            'variants.*.type' => 'required_with:variants|string|in:size,addon,milk,other',
            'variants.*.name' => 'required_with:variants|string|max:255',
            'variants.*.price_impact' => 'required_with:variants|numeric',
        ]);

        try {
            DB::beginTransaction(); // Mulai Transaksi

            $gambarPath = null;
            if ($request->hasFile('image')) {
                $gambarPath = $request->file('image')->store('products', 'public');
            }

            // 1. CREATE PRODUK UTAMA
            $product = Product::create([
                'name'          => $request->name,
                'slug'          => Str::slug($request->name),
                'description'   => $request->description,
                'price'         => $request->price,
                'stock'         => $request->stock,
                'category_id'   => $request->category_id,
                'image'         => $gambarPath,
            ]);

            // 2. CREATE VARIAN (Jika ada)
            if ($request->has('variants')) {
                $variantsData = [];
                foreach ($request->input('variants') as $variant) {
                    if (!empty($variant['name'])) {
                        $variantsData[] = new ProductVariant([
                            'type'         => $variant['type'],
                            'name'         => $variant['name'],
                            'price_impact' => (float) $variant['price_impact'],
                        ]);
                    }
                }
                // Simpan semua varian ke database sekaligus
                $product->variants()->saveMany($variantsData);
            }

            DB::commit(); // Selesaikan Transaksi

            return redirect()->route('admin.product.index')
                ->with('success', 'Data produk dan variannya berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan jika ada yang gagal
            Log::error('Store Product Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan produk: ' . $e->getMessage());
        }
    }

    // ... (Fungsi show tetap sama) ...

    /**
     * Form edit produk.
     */
    public function edit(Product $product)
    {
        try {
            // Eager load varian agar bisa diakses di view
            $product->load('variants');
            $categories = Category::all();
            return view('admin.product.edit', compact('product', 'categories'));
        } catch (\Exception $e) {
            Log::error('Edit Product Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuka form edit produk.');
        }
    }

    /**
     * Update produk dan variannya.
     */
    public function update(Request $request, Product $product)
    {
        // 1. Validasi Input
        $request->validate([
            // Validasi Produk Utama
            'name'        => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            // Validasi Varian
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer', // ID varian lama (bisa 0 untuk baru)
            'variants.*.type' => 'required_with:variants|string|in:size,addon,milk,other',
            'variants.*.name' => 'required_with:variants|string|max:255',
            'variants.*.price_impact' => 'required_with:variants|numeric',
        ]);

        try {
            DB::beginTransaction(); // Mulai Transaksi

            // --- A. UPDATE DATA PRODUK UTAMA ---

            $data = $request->only(['name', 'description', 'price', 'stock', 'category_id']);
            $data['slug'] = Str::slug($request->name);

            // Penanganan Gambar
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);


            // --- B. KELOLA VARIAN PRODUK (CRUD) ---

            $currentVariantIds = $product->variants->pluck('id')->toArray(); // ID Varian yang ada di DB
            $submittedVariantIds = []; // ID Varian yang diterima dari form

            if ($request->has('variants')) {
                foreach ($request->input('variants') as $variantData) {
                    $variantId = $variantData['id'] ?? 0;

                    $variantAttributes = [
                        'product_id'   => $product->id,
                        'type'         => $variantData['type'],
                        'name'         => $variantData['name'],
                        'price_impact' => (float) $variantData['price_impact'],
                    ];

                    if ($variantId > 0) {
                        // UPDATE Varian Lama (ID > 0)
                        $product->variants()->where('id', $variantId)->update($variantAttributes);
                        $submittedVariantIds[] = $variantId;
                    } else {
                        // CREATE Varian Baru (ID = 0 atau tidak ada)
                        $newVariant = ProductVariant::create($variantAttributes);
                        $submittedVariantIds[] = $newVariant->id;
                    }
                }
            }

            // 3. DELETE Varian yang Dihilangkan
            $variantsToDelete = array_diff($currentVariantIds, $submittedVariantIds);

            if (!empty($variantsToDelete)) {
                ProductVariant::whereIn('id', $variantsToDelete)->delete();
            }

            DB::commit(); // Selesaikan Transaksi

            return redirect()->route('admin.product.index')
                ->with('success', 'Data produk dan variannya berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua operasi jika ada yang gagal
            Log::error('Update Product Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    //hapus
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()->route('admin.product.index')
                ->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Destroy Product Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus produk.');
        }
    }
}
