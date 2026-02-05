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
            'sub_category_id' => 'nullable|exists:categories,id',
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
            'sub_category_id' => 'nullable|exists:categories,id',
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
        // Force Strict Dimensions
        $targetWidth = 800;
        $targetHeight = 600;

        \Illuminate\Support\Facades\Log::info("Processing Image: " . $file->getClientOriginalName() . " Target: {$targetWidth}x{$targetHeight}");
        
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
        
        // If GD fails or format unsupported, fallback to simple resize store (rare)
        if (!$sourceImage) {
            $storedPath = $file->storeAs($folder, $filename, 'public');
            return '/storage/' . $storedPath;
        }

        // Get Original Dimensions
        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        // Calculate Crop to Center-Fill 800x600
        // We want to cover the 800x600 area.
        $targetRatio = $targetWidth / $targetHeight;
        $originalRatio = $width / $height;

        $srcX = 0;
        $srcY = 0;
        $srcW = $width;
        $srcH = $height;

        if ($originalRatio > $targetRatio) {
            // Original is wider than target. Crop width.
            // Height matches, calculate new width based on target ratio relative to height
            $newSrcW = $height * $targetRatio;
            $srcX = ($width - $newSrcW) / 2;
            $srcW = $newSrcW;
        } else {
            // Original is taller than target. Crop height.
            // Width matches, calculate new height
            $newSrcH = $width / $targetRatio;
            $srcY = ($height - $newSrcH) / 2;
            $srcH = $newSrcH;
        }

        // Create canvas 800x600
        $finalImage = imagecreatetruecolor($targetWidth, $targetHeight);
        
        // Preserve Transparency
        if ($extension == 'png' || $extension == 'webp') {
            imagealphablending($finalImage, false);
            imagesavealpha($finalImage, true);
        } else {
            // Fill white background for JPG
            $white = imagecolorallocate($finalImage, 255, 255, 255);
            imagefill($finalImage, 0, 0, $white);
        }

        // Copy and Resize (Crop & Fill)
        imagecopyresampled($finalImage, $sourceImage, 0, 0, $srcX, $srcY, $targetWidth, $targetHeight, $srcW, $srcH);
        
        imagedestroy($sourceImage);

        // Save Compressed
        $quality = 80; 
        
        $saved = false;
        if ($extension == 'png') {
            $pngQuality = ($quality > 70) ? 6 : 8; 
            $saved = imagepng($finalImage, $fullPath, $pngQuality);
        } elseif ($extension == 'webp') {
            $saved = imagewebp($finalImage, $fullPath, $quality);
        } else {
            $saved = imagejpeg($finalImage, $fullPath, $quality);
        }

        imagedestroy($finalImage);

        if (!$saved) {
            // Fallback move
            $file->move(dirname($fullPath), basename($fullPath));
        }

        return '/storage/' . $path;
    }
}
