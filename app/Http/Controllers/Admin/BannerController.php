<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->get();
        // Allow adding if less than 5 active banners (or total banners, user requirement said "add up to five banner")
        // Interpretation: Total banners should be limited to 5 to keep it simple, or active ones.
        // Let's limit total banners to 5 for now as requested "add up to five".
        $canAdd = $banners->count() < 5;
        
        return view('admin.banners.index', compact('banners', 'canAdd'));
    }

    public function create()
    {
        if (Banner::count() >= 5) {
            return redirect()->route('admin.banners.index')->with('error', 'Maximum 5 banners allowed.');
        }
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        if (Banner::count() >= 5) {
            return redirect()->route('admin.banners.index')->with('error', 'Maximum 5 banners allowed.');
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB Max (Compressed later)
            'title' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:50',
            'link' => 'nullable|url',
            'order' => 'integer',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $this->compressAndStore($request->file('image'));
        }

        Banner::create($data);
        \Illuminate\Support\Facades\Cache::forget('home_banners');

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB Max
            'title' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:50',
            'link' => 'nullable|url',
            'order' => 'integer',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Delete old image if it exists
             if ($banner->image) {
                 $oldPath = str_replace('/storage/', 'public/', $banner->image);
                 if (Storage::exists($oldPath)) {
                     Storage::delete($oldPath);
                 }
             }
            
            $data['image'] = $this->compressAndStore($request->file('image'));
        }

        $banner->update($data);
        \Illuminate\Support\Facades\Cache::forget('home_banners');

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
             $oldPath = str_replace('/storage/', 'public/', $banner->image);
             if (Storage::exists($oldPath)) {
                 Storage::delete($oldPath);
             }
         }
        $banner->delete();
        \Illuminate\Support\Facades\Cache::forget('home_banners');
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }

    /**
     * Compress and Store Image
     * Automatically compresses if size > 5MB. But effectively optimizes all images.
     */
    private function compressAndStore($file)
    {
        // Generate name
        $extension = strtolower($file->guessExtension());
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            $extension = 'jpg';
        }
        $filename = \Illuminate\Support\Str::random(40) . '.' . $extension;
        $path = 'banners/' . $filename;
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
            $storedPath = $file->storeAs('banners', $filename, 'public');
            return '/storage/' . $storedPath;
        }

        $targetWidth = $width; // Default to original if no target set
        $targetHeight = $height; // Default to original

        // Define target dimensions based on type (heuristic or explicit arg if updated)
        // Since we want to reuse this for both Desktop (1920x450) and Mobile (e.g. 800x600)
        // We should pass dimensions as arguments.
        // For now, let's just make the method signature accept optional dimensions.
    }
}
        
        // Aggressive compression if original file > 5MB (5242880 bytes)
        if ($file->getSize() > 5242880) {
            $quality = 60; 
        }

        if ($extension == 'png') {
            // PNG Quality is 0-9 (inverted scaling of compression)
            $pngQuality = ($quality > 70) ? 6 : 8; 
            imagepng($sourceImage, $fullPath, $pngQuality);
        } elseif ($extension == 'webp') {
            imagewebp($sourceImage, $fullPath, $quality);
        } else {
            imagejpeg($sourceImage, $fullPath, $quality);
        }

        imagedestroy($sourceImage);

        return '/storage/' . $path;
    }
}
