<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EccommerceController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\IsAdmin;

Route::get('/', [EccommerceController::class, 'index'])->name('home');

Auth::routes();

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware'=> ['auth', IsAdmin::class]
], function () {

    Route::resource('category', CategoryController::class);
    Route::resource('product', ProductController::class);

});

 Route::get('/product/{id}/variants',
        [ProductController::class, 'getVariants']
    )->name('product.variants');

/*
|--------------------------------------------------------------------------
| User Auth Routes (EccommerceController Only)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth']], function () {

    // Ambil varian produk (frontend AJAX)
    Route::get('/api/product-variants/{productId}',
        [EccommerceController::class, 'getProductVariants']
    );

    // Keranjang & Order
    Route::post('/order', [EccommerceController::class, 'createOrder'])
        ->name('order.create');

    Route::post('/checkout', [EccommerceController::class, 'checkOut'])
        ->name('checkout');

    Route::get('/my-order', [EccommerceController::class, 'myOrders'])
        ->name('orders.my');

    Route::get('/my-order/{id}', [EccommerceController::class, 'orderDetail'])
        ->name('orders.detail');

    // Update + Remove item dari keranjang/order
    Route::post('/order/update-quantity',
        [EccommerceController::class, 'updateQuantity']
    )->name('order.update-quantity');

    Route::post('/order/remove-item',
        [EccommerceController::class, 'removeItem']
    )->name('order.remove-item');
});
