<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class OrderController extends Controller
{
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'temperature' => 'nullable|string',
            'sugar_level' => 'nullable|string',
            'ice_level' => 'nullable|string',
            'variant_details' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $cart = session()->get('cart', []);

        $product = Product::findOrFail($validated['product_id']);
        $variantKey = json_encode($validated['variant_details'] ?? []);
        $uniqueKey = $validated['product_id'] . '-' . md5($variantKey);

        $cart[$uniqueKey] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'temperature' => $validated['temperature'] ?? null,
            'sugar_level' => $validated['sugar_level'] ?? null,
            'ice_level' => $validated['ice_level'] ?? null,
            'variant_details' => $validated['variant_details'] ?? [],
            'notes' => $validated['notes'] ?? '',
        ];

        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Produk berhasil masuk ke keranjang 💚',
            'cart' => $cart,
        ]);
    }

    public function viewCart()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function removeFromCart($key)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang!');
    }
}
