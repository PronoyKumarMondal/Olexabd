<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {


        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'low_stock') {
                $query->where('stock', '<', 10);
            }
        }

        $products = $query->paginate(8);
        $categories = Category::all(); // For filter dropdown
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image_file' => 'nullable|image|max:5120',
            'image_url' => 'nullable|url',
            'discount_price' => 'nullable|numeric|lt:price',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after:discount_start',
        ]);
        
        $data = $request->except(['image_file', 'image_url']);
        $data['slug'] = Str::slug($request->name);
        
        // Handle Image
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = \Illuminate\Support\Str::random(40) . '.' . $file->guessExtension();
            $path = $file->storeAs('products', $filename, 'public');
            $data['image'] = '/storage/' . $path;
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->image_url;
        }

        $product = Product::create($data);

        // Handle Featured Images (Max 3)
        if ($request->hasFile('featured_images')) {
            $count = 0;
            foreach ($request->file('featured_images') as $file) {
                if ($count >= 3) break;
                
                $filename = 'feat_' . \Illuminate\Support\Str::random(40) . '.' . $file->guessExtension();
                $path = $file->storeAs('products/featured', $filename, 'public');
                
                $product->images()->create([
                    'image_path' => '/storage/' . $path,
                    // updated_by handled by Observer
                ]);
                $count++;
            }
        }

        \Illuminate\Support\Facades\Cache::forget('home_featured');
        \Illuminate\Support\Facades\Cache::forget('home_recent');

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image_file' => 'nullable|image|max:5120',
            'image_url' => 'nullable|url',
            'discount_price' => 'nullable|numeric|lt:price',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after:discount_start',
        ]);

        $data = $request->except(['image_file', 'image_url']);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        // Handle Image Update
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = \Illuminate\Support\Str::random(40) . '.' . $file->guessExtension();
            $path = $file->storeAs('products', $filename, 'public');
            $data['image'] = '/storage/' . $path;
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->image_url;
        }

        $product->update($data);

        // Delete Selected Images
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $img = $product->images()->find($imageId);
                if ($img) {
                    // Optional: Delete file from storage
                    $path = str_replace('/storage/', 'public/', $img->image_path);
                    if (\Illuminate\Support\Facades\Storage::exists($path)) {
                        \Illuminate\Support\Facades\Storage::delete($path);
                    }
                    $img->delete();
                }
            }
        }

        // Add New Featured Images
        $currentCount = $product->images()->count();
        if ($request->hasFile('featured_images')) {
            foreach ($request->file('featured_images') as $file) {
                if ($currentCount >= 3) break;
                
                $filename = 'feat_' . \Illuminate\Support\Str::random(40) . '.' . $file->guessExtension();
                $path = $file->storeAs('products/featured', $filename, 'public');
                
                $product->images()->create([
                    'image_path' => '/storage/' . $path,
                ]);
                $currentCount++;
            }
        }

        \Illuminate\Support\Facades\Cache::forget('home_featured');
        \Illuminate\Support\Facades\Cache::forget('home_recent');

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();
        \Illuminate\Support\Facades\Cache::forget('home_featured');
        \Illuminate\Support\Facades\Cache::forget('home_recent');
        return redirect()->back()->with('success', 'Product deleted.');
    }
}
