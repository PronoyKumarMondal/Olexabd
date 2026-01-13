<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = [];

        // Static Pages
        $posts[] = [
            'loc' => route('shop.index'),
            'lastmod' => now()->toAtomString(),
            'priority' => '1.0',
            'changefreq' => 'daily'
        ];
        
         $posts[] = [
            'loc' => route('shop.products'),
            'lastmod' => now()->toAtomString(),
            'priority' => '0.8',
            'changefreq' => 'daily'
        ];

        // Categories
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $category) {
            $posts[] = [
                'loc' => route('shop.category', $category),
                'lastmod' => $category->updated_at->toAtomString(),
                'priority' => '0.8',
                'changefreq' => 'weekly'
            ];
        }

        // Products
        $products = Product::where('is_active', true)->get();
        foreach ($products as $product) {
            $posts[] = [
                'loc' => route('shop.show', $product->slug),
                'lastmod' => $product->updated_at->toAtomString(),
                'priority' => '0.9',
                'changefreq' => 'weekly'
            ];
        }

        return response()->view('sitemap.index', [
            'posts' => $posts,
        ])->header('Content-Type', 'text/xml');
    }
}
