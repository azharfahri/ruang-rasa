<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\OrderController as KasirOrderController;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\VariantTypeController;
use App\Http\Controllers\VariantOptionController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BranchProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\UserController;

Auth::routes();

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (ADMIN ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('user-roles', UserRoleController::class)->only(['index','edit','update']);

    Route::resource('products', ProductController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('branch-products', BranchProductController::class);

    // VARIANT TYPE (PER PRODUCT)
    Route::prefix('products/{product}')->group(function () {
        Route::get('variant-types', [VariantTypeController::class, 'index'])
            ->name('product.variant-types.index');

        Route::get('variant-types/create', [VariantTypeController::class, 'create'])
            ->name('product.variant-types.create');

        Route::post('variant-types', [VariantTypeController::class, 'store'])
            ->name('product.variant-types.store');

        Route::get('variant-types/{variantType}/edit', [VariantTypeController::class, 'edit'])
            ->name('product.variant-types.edit');

        Route::put('variant-types/{variantType}', [VariantTypeController::class, 'update'])
            ->name('product.variant-types.update');

        Route::delete('variant-types/{variantType}', [VariantTypeController::class, 'destroy'])
            ->name('product.variant-types.destroy');
    });

    // VARIANT OPTION (PER VARIANT TYPE)
    Route::prefix('variant-types/{variantType}')->group(function () {
        Route::get('options', [VariantOptionController::class, 'index'])
            ->name('variant-types.options.index');

        Route::get('options/create', [VariantOptionController::class, 'create'])
            ->name('variant-types.options.create');

        Route::post('options', [VariantOptionController::class, 'store'])
            ->name('variant-types.options.store');

        Route::get('options/{option}/edit', [VariantOptionController::class, 'edit'])
            ->name('variant-types.options.edit');

        Route::put('options/{option}', [VariantOptionController::class, 'update'])
            ->name('variant-types.options.update');

        Route::delete('options/{option}', [VariantOptionController::class, 'destroy'])
            ->name('variant-types.options.destroy');
    });

});

/*
|--------------------------------------------------------------------------
| KASIR ROUTES (KASIR ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','role:cashier'])->group(function () {

    Route::get('/cashier', [KasirDashboardController::class,'index'])
        ->name('cashier.dashboard');

    Route::get('/cashier/orders', [KasirOrderController::class,'index'])
        ->name('cashier.orders.index');

    Route::get('/cashier/orders/create', [KasirOrderController::class,'create'])
        ->name('cashier.orders.create');

    Route::post('/cashier/orders/{order}/add-item', [KasirOrderController::class,'addItem'])
        ->name('cashier.orders.addItem');

    Route::post('/cashier/orders/{order}/pay-cash', [KasirOrderController::class,'payCash'])
        ->name('cashier.orders.pay.cash');

    Route::get('/cashier/orders/history', [KasirOrderController::class,'history'])
        ->name('cashier.orders.history');
});