<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use App\Models\Order;
use App\Policies\OrderPolicy;
use App\Models\Product;
use App\Policies\ProductPolicy;
use App\Models\Category;
use App\Policies\CategoryPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Super Admin Gate
        Gate::define('super_admin', function ($user) {
            return $user->role === 'super_admin';
        });

        // Grant Super Admin all access
        Gate::before(function ($user, $ability) {
            if ($user->role === 'super_admin') {
                return true;
            }
        });

        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);

        // Register Audit Observers
        Product::observe(\App\Observers\AuditObserver::class);
        Category::observe(\App\Observers\AuditObserver::class);
        Order::observe(\App\Observers\AuditObserver::class);
        \App\Models\PromoCode::observe(\App\Observers\AuditObserver::class);
        \App\Models\ProductImage::observe(\App\Observers\AuditObserver::class);
        
        // Vite::prefetch(concurrency: 3);

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $view->with('globalCategories', \Illuminate\Support\Facades\Cache::remember('global_categories', 3600, function () {
                // Fixed: Remove 'order' column sort as it doesn't exist
                return \App\Models\Category::where('is_active', true)
                    ->whereNull('parent_id')
                    ->with(['children' => function($q) {
                        $q->where('is_active', true)->orderBy('name');
                    }])
                    ->orderBy('name')
                    ->get();
            }));
        });
    }
}
