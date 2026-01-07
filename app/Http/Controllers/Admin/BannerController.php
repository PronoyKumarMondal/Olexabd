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
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:50',
            'link' => 'nullable|url',
            'order' => 'integer',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $data['image'] = '/storage/' . $path;
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:50',
            'link' => 'nullable|url',
            'order' => 'integer',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Delete old image if needed (optional)
            // if ($banner->image) { ... }
            
            $path = $request->file('image')->store('banners', 'public');
            $data['image'] = '/storage/' . $path;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
