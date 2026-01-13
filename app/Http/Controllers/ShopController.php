<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $banners = \Illuminate\Support\Facades\Cache::remember('home_banners', 60*60, function () {
            return \App\Models\Banner::where('is_active', true)->orderBy('order')->get();
        });

        $categories = \Illuminate\Support\Facades\Cache::remember('home_categories', 60*60, function () {
            return Category::where('is_active', true)->whereNull('parent_id')->withCount('products')->get();
        });
        
        // Featured: 8 items
        $featuredProducts = \Illuminate\Support\Facades\Cache::remember('home_featured', 30*60, function () {
            return Product::where('is_active', true)
                ->with('category')
                ->inRandomOrder() 
                ->take(8)
                ->get();
        });

        // All/Recent: 11 items
        $products = \Illuminate\Support\Facades\Cache::remember('home_recent', 30*60, function () {
            return Product::where('is_active', true)
                ->with('category')
                ->latest()
                ->take(11)
                ->get();
        });

        return view('shop.index', compact('banners', 'categories', 'featuredProducts', 'products'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->increment('views');
        
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }

    public function category(Category $category)
    {
        $title = $category->name;
        $children = $category->children()->where('is_active', true)->withCount('products')->get();
        
        $products = $category->products()
            ->where('is_active', true)
            ->latest()
            ->paginate(12);
            
        return view('shop.listing', compact('title', 'products', 'children', 'category'));
    }

    public function featured()
    {
        $title = "Featured Products";
        $products = Product::where('is_active', true)
            ->inRandomOrder()
            ->paginate(12);

        return view('shop.listing', compact('title', 'products'));
    }

    public function products()
    {
        $title = "All Products";
        $products = Product::where('is_active', true)
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('shop.listing', compact('title', 'products'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $products = Product::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('category')
            ->latest()
            ->paginate(12);
            
        $recommendedProducts = Product::where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Log Search History
        if (!empty($query)) {
            \App\Models\SearchHistory::create([
                'user_id' => auth()->id(), // Null if guest
                'query' => $query,
                'ip_address' => $request->ip(),
                'results_count' => $products->total(),
            ]);
        }

        return view('shop.search', compact('products', 'query', 'recommendedProducts'));
    }
    public function serveStorage(Request $request, $path)
    {
        // Sanitize path to prevent directory traversal
        if (str_contains($path, '..')) {
            abort(403);
        }

        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    }
}
