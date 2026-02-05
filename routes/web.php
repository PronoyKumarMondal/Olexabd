<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ADMIN ROUTES (Must be first to capture subdomain)
Route::domain(env('APP_ADMIN_DOMAIN', 'admin.olexabd.com'))->name('admin.')->group(function () {
    
    // Admin Auth
    Route::get('/login', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'create'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'store'])->name('login.store');
    Route::post('/logout', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'destroy'])->name('logout');

    // Storage Proxy
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
        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)->only(['index', 'create', 'store']);
        Route::resource('channels', \App\Http\Controllers\Admin\ChannelController::class);

        // Super Admin Only
        Route::get('/super', [App\Http\Controllers\Admin\SuperAdminController::class, 'index'])->name('super.index');
        Route::post('/super/store', [App\Http\Controllers\Admin\SuperAdminController::class, 'store'])->name('super.store');
        Route::put('/super/{user}/role', [App\Http\Controllers\Admin\SuperAdminController::class, 'updateRole'])->name('super.update_role');
        Route::get('/super/health', [App\Http\Controllers\Admin\SuperAdminController::class, 'health'])->name('super.health');
        
        // Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    });
});


// FRONTEND ROUTES (Catch-all for main domain)
// Route::domain(env('APP_FRONTEND_DOMAIN', 'www.olexabd.com'))->group(function () {
Route::group([], function () {
    
    // Front-end Storage Proxy
    Route::get('/storage/{path}', [\App\Http\Controllers\ShopController::class, 'serveStorage'])
        ->where('path', '.*')
        ->name('shop.storage.proxy');

    // Remove conflicting Welcome route
    /*
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    });
    */

    // Dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('shop.index');
    })->middleware(['auth', 'verified'])->name('dashboard');

    // Static Pages
    Route::controller(\App\Http\Controllers\PageController::class)->group(function () {
        Route::get('/contact-us', 'contact')->name('pages.contact');
        Route::get('/return-warranty-policy', 'returnWarranty')->name('pages.return_warranty');
        Route::get('/terms-conditions', 'terms')->name('pages.terms');
        Route::get('/privacy-policy', 'privacy')->name('pages.privacy');
    });

    // Tracking
    Route::get('/track-order', [OrderController::class, 'track'])->name('orders.track');
    Route::post('/track-order', [OrderController::class, 'trackOrder'])->name('orders.track.submit');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/account/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/account/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });

    // Shop Routes (Notice: This defines '/' so it must be careful)
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

    // Location API Routes (Public)
    Route::get('/api/locations/divisions', [\App\Http\Controllers\LocationsController::class, 'getDivisions']);
    Route::get('/api/locations/districts/{division_id}', [\App\Http\Controllers\LocationsController::class, 'getDistricts']);
    Route::get('/api/locations/upazilas/{district_id}', [\App\Http\Controllers\LocationsController::class, 'getUpazilas']);
    Route::get('/api/locations/postcode/{upazila_id}', [\App\Http\Controllers\LocationsController::class, 'getPostcode']);

    // Debug SMTP
    Route::get('/test/email', function() {
        try {
            \Illuminate\Support\Facades\Mail::raw('SMTP Test Success!', function($msg) {
                $msg->to('office.pronoy@gmail.com')
                    ->subject('Test Email');
            });
            return 'Email Sent Successfully!';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    });

    // Google Auth Routes
    Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback']);
    
    // Phone Verification Routes
    Route::get('/auth/phone/verify', [\App\Http\Controllers\Auth\SocialAuthController::class, 'showPhoneForm'])->name('auth.phone.form');
    Route::post('/auth/phone/save', [\App\Http\Controllers\Auth\SocialAuthController::class, 'savePhone'])->name('auth.phone.save');

    Route::middleware('auth')->group(function() {
        // Checkout Routes
        Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.page');
        Route::post('/checkout/place', [\App\Http\Controllers\CheckoutController::class, 'placeOrder'])->name('checkout.place');
        
        Route::post('/checkout/init', function() {
            return redirect()->route('checkout.page');
        })->name('checkout.init');

        Route::get('/bkash/mock', [\App\Http\Controllers\PaymentController::class, 'mockPage'])->name('bkash.mock_page');
        Route::post('/bkash/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('bkash.success');
    });

    require __DIR__.'/auth.php';

    Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
});

// 4. Link Storage (For Images)
// \Artisan::call('storage:link');