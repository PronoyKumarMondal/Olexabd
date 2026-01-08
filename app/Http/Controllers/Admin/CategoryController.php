<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with(['parent', 'children'])->withCount('products')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $categories = $query->paginate(10);
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get(); // Only allowing 1 level deep for now for simplicity, or fetching all to allow deep nesting? User asked for mapping. Typically main dropdown has all.
        // Let's pass all categories for the dropdown, but typically we exclude the category itself in update.
        // For index view dropdown, we just need a list of potential parents.
        $allCategories = Category::orderBy('name')->get();
        
        return view('admin.categories.index', compact('categories', 'allCategories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Category::class);
        $request->validate([
            'name' => 'required|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
            'image_file' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url'
        ]);

        $image = null;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $image = '/storage/' . $path;
        } elseif ($request->filled('image_url')) {
            $image = $request->image_url;
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
            'image' => $image,
            'is_active' => true
        ]);

        \Illuminate\Support\Facades\Cache::forget('home_categories');

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id, // Prevent self-parenting
            'image_file' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url'
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
        ];

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $data['image'] = '/storage/' . $path;
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->image_url;
        }

        $category->update($data);
        \Illuminate\Support\Facades\Cache::forget('home_categories');

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();
        \Illuminate\Support\Facades\Cache::forget('home_categories');
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
