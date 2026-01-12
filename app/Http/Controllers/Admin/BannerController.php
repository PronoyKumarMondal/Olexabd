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
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->hasPermission('manage_banners')) {
            abort(403, 'Unauthorized action.');
        }

        $banners = Banner::orderBy('order')->get();
        // Allow adding if less than 5 active banners
        $canAdd = $banners->count() < 5;
        
        return view('admin.banners.index', compact('banners', 'canAdd'));
    }

    public function create()
    {
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->hasPermission('manage_banners')) {
            abort(403, 'Unauthorized action.');
        }

        if (Banner::count() >= 5) {
            return redirect()->route('admin.banners.index')->with('error', 'Maximum 5 banners allowed.');
        }
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->hasPermission('manage_banners')) {
            abort(403, 'Unauthorized action.');
        }

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
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->hasPermission('manage_banners')) {
            abort(403, 'Unauthorized action.');
        }
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->hasPermission('manage_banners')) {
            abort(403, 'Unauthorized action.');
        }

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
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->hasPermission('manage_banners')) {
            abort(403, 'Unauthorized action.');
        }

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
     */
    private function compressAndStore($file, $targetWidth = 1920, $targetHeight = 450)
    {
        // Generate Name
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

        // Native Compression
        $sourceImage = null;
        $mime = $file->getMimeType();

        if ($extension == 'jpeg' || $extension == 'jpg' || $mime == 'image/jpeg') 
            $sourceImage = @imagecreatefromjpeg($file->getRealPath());
        elseif ($extension == 'png' || $mime == 'image/png')
            $sourceImage = @imagecreatefrompng($file->getRealPath());
        elseif ($extension == 'webp' || $mime == 'image/webp')
             $sourceImage = @imagecreatefromwebp($file->getRealPath());
        
        // Fallback
        if (!$sourceImage) {
            $storedPath = $file->storeAs('banners', $filename, 'public');
            return '/storage/' . $storedPath;
        }

        // Resize
        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);
        
        $srcRatio = $width / $height;
        $targetRatio = $targetWidth / $targetHeight;
        
        if ($srcRatio > $targetRatio) {
            $newW = $targetWidth;
            $newH = $targetWidth / $srcRatio;
        } else {
            $newH = $targetHeight;
            $newW = $targetHeight * $srcRatio;
        }

        $dstX = ($targetWidth - $newW) / 2;
        $dstY = ($targetHeight - $newH) / 2;

        $finalImage = imagecreatetruecolor($targetWidth, $targetHeight);
        $white = imagecolorallocate($finalImage, 255, 255, 255);
        imagefill($finalImage, 0, 0, $white);

        if ($extension == 'png' || $extension == 'webp') {
            imagealphablending($finalImage, false);
            imagesavealpha($finalImage, true);
        }

        imagecopyresampled($finalImage, $sourceImage, $dstX, $dstY, 0, 0, $newW, $newH, $width, $height);
        
        imagedestroy($sourceImage);
        $sourceImage = $finalImage;

        // Compression
        $quality = 80; 
        if ($file->getSize() > 2097152) {
            $quality = 60; 
        }

        if ($extension == 'png') {
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
