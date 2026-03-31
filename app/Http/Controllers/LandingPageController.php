<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // Ambil 6 produk terbaru atau acak untuk ditampilkan di Landing Page
        $products = Product::latest()->take(6)->get();

        return view('welcome', compact('products'));
    }
}
