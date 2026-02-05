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
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->hasPermission('product_create')) {
            abort(403, 'Unauthorized action.');
        }
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->hasPermission('product_create')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image_file' => 'nullable|image|max:10240', // 10MB
            'image_url' => 'nullable|url',
            'discount_price' => 'nullable|numeric|lt:price',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after:discount_start',
        ]);
        
        
        $data = $request->except(['image_file', 'image_url']);
        $data['slug'] = Str::slug($request->name);
        
        // Handle Main Image (Compress > 2MB)
        if ($request->hasFile('image_file')) {
            $data['image'] = $this->compressAndStore($request->file('image_file'), 'products', 2097152, 1200);
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->image_url;
        }

        // Calculate missing Commission Data for integrity
        $price = $request->price;
        if ($price > 0) {
            if (isset($data['commission_percentage']) && !isset($data['commission_amount'])) {
                $data['commission_amount'] = ($price * $data['commission_percentage']) / 100;
            } elseif (isset($data['commission_amount']) && !isset($data['commission_percentage'])) {
                $data['commission_percentage'] = ($data['commission_amount'] / $price) * 100;
            }
        }

        $product = Product::create($data);

        // Handle Featured Images (Max 3) (Compress > 1MB)
        if ($request->hasFile('featured_images')) {
            $count = 0;
            foreach ($request->file('featured_images') as $file) {
                if ($count >= 3) break;
                if (!$file) continue; // Skip empty inputs
                
                $path = $this->compressAndStore($file, 'products/featured', 1048576, 1000);
                
                $product->images()->create([
                    'image_path' => $path,
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
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->hasPermission('product_edit')) {
            abort(403, 'Unauthorized action.');
        }
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->hasPermission('product_edit')) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image_file' => 'nullable|image|max:10240', // 10MB
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
            $data['image'] = $this->compressAndStore($request->file('image_file'), 'products', 2097152, 1200);
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->image_url;
        }

        // Calculate missing Commission Data for integrity
        $price = $request->price;
        if ($price > 0) {
            // Check if keys exist in $data (means they were in request)
            // Note: If user cleared the input, it might be null.
            // We only calc if one is present and non-null, and other is null/missing?
            // Actually, best to check if one has value > 0 and other is missing/null.
            $commP = $data['commission_percentage'] ?? null;
            $commA = $data['commission_amount'] ?? null;

            if ($commP !== null && $commA === null) {
                $data['commission_amount'] = ($price * $commP) / 100;
            } elseif ($commA !== null && $commP === null) {
                $data['commission_percentage'] = ($commA / $price) * 100;
            }
            // If both are set, we assume frontend calc is correct or user intentions.
            // If both null, nothing to do.
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

        if ($request->hasFile('featured_images')) {
            $currentCount = $product->images()->count();
            foreach ($request->file('featured_images') as $file) {
                if ($currentCount >= 3) break;
                if (!$file) continue;
                
                $path = $this->compressAndStore($file, 'products/featured', 1048576, 1000);
                
                $product->images()->create([
                    'image_path' => $path,
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
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->hasPermission('product_delete')) {
            abort(403, 'Unauthorized action.');
        }
        $product->delete();
        \Illuminate\Support\Facades\Cache::forget('home_featured');
        \Illuminate\Support\Facades\Cache::forget('home_recent');
        return redirect()->back()->with('success', 'Product deleted.');
    }

    /**
     * Compress Image Helper
     */
    private function compressAndStore($file, $folder, $maxSizeBytes, $maxWidth = 1500)
    {
        \Illuminate\Support\Facades\Log::info("Compressing: " . $file->getClientOriginalName() . " Size: " . $file->getSize());
        
        // Generate name
        $extension = strtolower($file->guessExtension());
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            $extension = 'jpg';
        }
        $filename = \Illuminate\Support\Str::random(40) . '.' . $extension;
        $path = $folder . '/' . $filename;
        $fullPath = storage_path('app/public/' . $path);
        
        // Ensure directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        // Native Compression Logic
        $sourceImage = null;
        $mime = $file->getMimeType();

        // Load Image
        if ($extension == 'jpeg' || $extension == 'jpg' || $mime == 'image/jpeg') 
            $sourceImage = @imagecreatefromjpeg($file->getRealPath());
        elseif ($extension == 'png' || $mime == 'image/png')
            $sourceImage = @imagecreatefrompng($file->getRealPath());
        elseif ($extension == 'webp' || $mime == 'image/webp')
             $sourceImage = @imagecreatefromwebp($file->getRealPath());
        
        // If GD fails or format unsupported, fallback to standard store
        if (!$sourceImage) {
            \Illuminate\Support\Facades\Log::warning("GD Fallback used for: " . $file->getClientOriginalName());
            $storedPath = $file->storeAs($folder, $filename, 'public');
            return '/storage/' . $storedPath;
        }

        // Resize if wider than maxWidth
        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = floor($height * ($maxWidth / $width));
            $tempImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve Transparency
            if ($extension == 'png' || $extension == 'webp') {
                imagealphablending($tempImage, false);
                imagesavealpha($tempImage, true);
            }

            imagecopyresampled($tempImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($sourceImage);
            $sourceImage = $tempImage;
        }

        // Save Compressed
        $quality = 80; 
        
        // Aggressive compression if original file > maxSizeBytes
        if ($file->getSize() > $maxSizeBytes) {
            $quality = 60; 
            \Illuminate\Support\Facades\Log::info("Applying Aggressive Compression (Quality 60) for: " . $filename);
        }

        $saved = false;
        if ($extension == 'png') {
            $pngQuality = ($quality > 70) ? 6 : 8; 
            $saved = imagepng($sourceImage, $fullPath, $pngQuality);
        } elseif ($extension == 'webp') {
            $saved = imagewebp($sourceImage, $fullPath, $quality);
        } else {
            $saved = imagejpeg($sourceImage, $fullPath, $quality);
        }

        imagedestroy($sourceImage);

        if (!$saved) {
            \Illuminate\Support\Facades\Log::error("Failed to save compressed image to: " . $fullPath);
            // Fallback to simple move if compression saving failed
            $file->move(dirname($fullPath), basename($fullPath));
            return '/storage/' . $path;
        }

        if (file_exists($fullPath)) {
            \Illuminate\Support\Facades\Log::info("Saved Compressed: " . $filename . " New Size: " . filesize($fullPath));
        }

        return '/storage/' . $path;
    }
}
