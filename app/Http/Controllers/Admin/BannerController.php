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
        // Allow adding if less than 5 active banners
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
            'mobile_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // Required
            'title' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:50',
            'link' => 'nullable|url',
            'order' => 'integer',
        ]);

        $data = $request->except(['image', 'mobile_image']);

        // Desktop Image
        if ($request->hasFile('image')) {
            $data['image'] = $this->compressAndStore($request->file('image'), 1920, 450);
        }

        // Mobile Image
        if ($request->hasFile('mobile_image')) {
            $data['mobile_image'] = $this->compressAndStore($request->file('mobile_image'), 800, 600);
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
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'title' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:50',
            'link' => 'nullable|url',
            'order' => 'integer',
        ]);

        $data = $request->except(['image', 'mobile_image']);

        // Desktop Image
        if ($request->hasFile('image')) {
             if ($banner->image) {
                 $oldPath = str_replace('/storage/', 'public/', $banner->image);
                 if (Storage::exists($oldPath)) { Storage::delete($oldPath); }
             }
            $data['image'] = $this->compressAndStore($request->file('image'), 1920, 450);
        }
        
        // Mobile Image
        if ($request->hasFile('mobile_image')) {
             if ($banner->mobile_image) {
                 $oldPath = str_replace('/storage/', 'public/', $banner->mobile_image);
                 if (Storage::exists($oldPath)) { Storage::delete($oldPath); }
             }
            $data['mobile_image'] = $this->compressAndStore($request->file('mobile_image'), 800, 600);
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
        if ($banner->mobile_image) {
             $oldPath = str_replace('/storage/', 'public/', $banner->mobile_image);
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
    private function compressAndStore($file, $targetWidth = 1920, $targetHeight = 450)
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

        // Resize & Pad Logic
        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);
        
        $srcRatio = $width / $height;
        $targetRatio = $targetWidth / $targetHeight;
        
        // Calculate dimensions to FIT inside target
        if ($srcRatio > $targetRatio) {
            // Wider than target: Limit by Width
            $newW = $targetWidth;
            $newH = $targetWidth / $srcRatio;
        } else {
            // Taller than target: Limit by Height
            $newH = $targetHeight;
            $newW = $targetHeight * $srcRatio;
        }

        // Calculate Centering Offsets
        $dstX = ($targetWidth - $newW) / 2;
        $dstY = ($targetHeight - $newH) / 2;

        // Create Canvas (White Background)
        $finalImage = imagecreatetruecolor($targetWidth, $targetHeight);
        $white = imagecolorallocate($finalImage, 255, 255, 255);
        imagefill($finalImage, 0, 0, $white);

        // Preserve Transparency
        if ($extension == 'png' || $extension == 'webp') {
            imagealphablending($finalImage, false);
            imagesavealpha($finalImage, true);
        }

        // Resample (Resize & Center)
        imagecopyresampled(
            $finalImage, $sourceImage, 
            $dstX, $dstY, 
            0, 0, 
            $newW, $newH, 
            $width, $height
        );
        
        imagedestroy($sourceImage);
        $sourceImage = $finalImage;

        // Save Compressed
        $quality = 80; // Default good quality
        
        // Aggressive compression if original file > 2MB (2097152 bytes)
        if ($file->getSize() > 2097152) {
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
