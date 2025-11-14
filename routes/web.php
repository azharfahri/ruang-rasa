<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EccommerceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController; // <--- tambahin ini
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\IsAdmin;

Route::get('/', [EccommerceController::class, 'index'])->name('home');

Auth::routes();

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware'=> ['auth', IsAdmin::class]
], function () {
    Route::resource('category', CategoryController::class);
    Route::resource('product', ProductController::class);
});

Route::get('/product/{id}/variants', [ProductController::class, 'getVariants'])->name('product.variants');

Route::group(['middleware' => ['auth']], function () {
    // Varian produk

    Route::get('/api/product-variants/{productId}', [EccommerceController::class, 'getProductVariants']);

    // Order
    Route::post('/order', [EccommerceController::class, 'createOrder'])->name('order.create');
    Route::post('/checkout', [EccommerceController::class, 'checkOut'])->name('checkout');
    Route::get('/my-order', [EccommerceController::class, 'myOrders'])->name('orders.my');
    Route::get('/my-order/{id}', [EccommerceController::class, 'orderDetail'])->name('orders.detail');
    Route::post('/order/update-quantity', [EccommerceController::class, 'updateQuantity'])->name('order.update-quantity');
    Route::post('/order/remove-item', [EccommerceController::class, 'removeItem'])->name('order.remove-item');

    // Cart — dari OrderController
    Route::post('/cart/add', [OrderController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [OrderController::class, 'viewCart'])->name('cart.view');
    Route::delete('/cart/remove/{key}', [OrderController::class, 'removeFromCart'])->name('cart.remove');
    Route::delete('/order/remove-item', [OrderController::class, 'removeItem'])->name('orders.removeItem');
    Route::post('/order/checkout', [OrderController::class, 'checkOut'])->name('orders.checkOut');
});
