<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $banners = \App\Models\Banner::where('is_active', true)->orderBy('order')->get();
        $categories = Category::where('is_active', true)->whereNull('parent_id')->withCount('products')->get();
        
        // Featured: 8 items
        $featuredProducts = Product::where('is_active', true)
            ->inRandomOrder() // Or add 'is_featured' column check if available
            ->take(8)
            ->get();

        // All/Recent: 11 items (12th slot is for "View All" card)
        $products = Product::where('is_active', true)
            ->latest()
            ->take(11)
            ->get();

        return view('shop.index', compact('banners', 'categories', 'featuredProducts', 'products'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }
        
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
            ->latest()
            ->paginate(12);
            
        $recommendedProducts = Product::where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

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
