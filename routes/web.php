<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Main Site Routes (Dynamic Domain)
Route::domain(env('APP_FRONTEND_DOMAIN', 'www.olexabd.com'))->group(function () {
    
    // Front-end Storage Proxy (Bypass Symlinks)
    Route::get('/storage/{path}', [\App\Http\Controllers\ShopController::class, 'serveStorage'])
        ->where('path', '.*')
        ->name('shop.storage.proxy');

    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    });

    // Dashboard (Redirect to Profile or Home)
    Route::get('/dashboard', function () {
        return redirect()->route('shop.index');
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
    });

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
        Route::post('/cart/apply-promo', 'applyPromo')->name('cart.apply_promo');
        Route::post('/cart/remove-promo', 'removePromo')->name('cart.remove_promo');
    });

    Route::middleware('auth')->group(function() {
        Route::post('/checkout', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('checkout.init');
        Route::get('/bkash/mock', [\App\Http\Controllers\PaymentController::class, 'mockPage'])->name('bkash.mock_page');
        Route::post('/bkash/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('bkash.success');
    });

    require __DIR__.'/auth.php';
});

// Admin Routes (Dynamic Domain)
Route::domain(env('APP_ADMIN_DOMAIN', 'admin.olexabd.com'))->name('admin.')->group(function () {
    
    // Admin Auth
    Route::get('/login', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'create'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'store'])->name('login.store');
    Route::post('/logout', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'destroy'])->name('logout');

    // Storage Proxy (Bypasses Symlink Issues)
    Route::get('/storage/{path}', [\App\Http\Controllers\Admin\DashboardController::class, 'serveStorage'])
        ->where('path', '.*')
        ->name('storage.proxy');

    // Admin Protected Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
        Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);
        Route::resource('promos', \App\Http\Controllers\Admin\PromoCodeController::class);
        Route::get('/cart-history', [\App\Http\Controllers\Admin\CartHistoryController::class, 'index'])->name('cart_history.index');
        Route::get('/search-history', [\App\Http\Controllers\Admin\SearchHistoryController::class, 'index'])->name('search_history.index');
        Route::get('/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::resource('channels', \App\Http\Controllers\Admin\ChannelController::class);

        // Super Admin Only
        Route::get('/super', [App\Http\Controllers\Admin\SuperAdminController::class, 'index'])->name('super.index');
        Route::post('/super/store', [App\Http\Controllers\Admin\SuperAdminController::class, 'store'])->name('super.store');
        Route::put('/super/{user}/role', [App\Http\Controllers\Admin\SuperAdminController::class, 'updateRole'])->name('super.update_role');
        Route::get('/super/health', [App\Http\Controllers\Admin\SuperAdminController::class, 'health'])->name('super.health');
    });
});

// 4. Link Storage (For Images)
// \Artisan::call('storage:link');