<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Tracking (Public)
Route::get('/track-order', [OrderController::class, 'track'])->name('orders.track');
Route::post('/track-order', [OrderController::class, 'trackOrder'])->name('orders.track.submit');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Order History
    Route::get('/account/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/account/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
        Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);
        
        // Customers
        Route::get('/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');

        // Super Admin Only
        Route::middleware(['can:super_admin'])->group(function () {
            Route::get('/super', [App\Http\Controllers\Admin\SuperAdminController::class, 'index'])->name('super.index');
            Route::post('/super/store', [App\Http\Controllers\Admin\SuperAdminController::class, 'store'])->name('super.store');
            Route::put('/super/{user}/role', [App\Http\Controllers\Admin\SuperAdminController::class, 'updateRole'])->name('super.update_role');
            Route::get('/super/health', [App\Http\Controllers\Admin\SuperAdminController::class, 'health'])->name('super.health');
        });
    });
});

// Shop Routes
// Shop Routes
Route::controller(\App\Http\Controllers\ShopController::class)->group(function () {
    Route::get('/', 'index')->name('shop.index');
    Route::get('/product/{product:slug}', 'show')->name('shop.show');
    Route::get('/category/{category:slug}', 'category')->name('shop.category');
    Route::get('/featured', 'featured')->name('shop.featured');
    Route::get('/products', 'products')->name('shop.products');
    Route::get('/search', 'search')->name('shop.search');
});

// Cart Routes
Route::controller(\App\Http\Controllers\CartController::class)->group(function () {
    Route::get('/cart', 'index')->name('cart.index');
    Route::post('/cart/add', 'addToCart')->name('cart.add');
    Route::patch('/cart/update', 'updateCart')->name('cart.update');
    Route::delete('/cart/remove', 'remove')->name('cart.remove');
});

Route::middleware('auth')->group(function() {
    Route::post('/checkout', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('checkout.init');
    Route::get('/bkash/mock', [\App\Http\Controllers\PaymentController::class, 'mockPage'])->name('bkash.mock_page');
    Route::post('/bkash/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('bkash.success');
});

require __DIR__.'/auth.php';


// 4. Link Storage (For Images)
    // \Artisan::call('storage:link');
    // return 'Setup Completed!';
// });
Route::get('/server-setup', function () {
    // 1. Install Dependencies (Might take time)
    // Note: Hostinger usually blocks 'composer install' via web. 
    // IF THIS FAILS, USE 'Option B' below.
    // exec('composer install --no-dev --optimize-autoloader');

    // 2. Clear Caches
    \Artisan::call('optimize:clear');
    \Artisan::call('config:clear');

    // 3. Migrate Database (Create Tables)
    \Artisan::call('migrate:fresh --seed --force');

    // 4. Link Storage (For Images)
    \Artisan::call('storage:link');

    return 'Setup Completed! <br> 1. Cache Cleared <br> 2. Database Migrated <br> 3. Storage Linked';
});