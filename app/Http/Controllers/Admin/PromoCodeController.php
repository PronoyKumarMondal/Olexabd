<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function __construct()
    {
        // Enforce 'manage_promos' permission on all actions
        // But allow Super Admin to bypass via the 'admin' middleware logic or Policy
        // Using a closure or Gate check. 
        // Best approach: Use middleware that checks permission.
        // Assuming we have a 'can' middleware or we check in method.
        // Let's use simple logic: Abort if not Super Admin AND no permission.
        $this->middleware(function ($request, $next) {
            $admin = auth('admin')->user();
            if (!$admin) {
                return redirect()->route('admin.login');
            }
            if ($admin->role !== 'super_admin' && !in_array('manage_promos', $admin->permissions ?? [])) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $promos = PromoCode::latest()->get();
        return view('admin.promos.index', compact('promos'));
    }

    public function create()
    {
        $categories = \App\Models\Category::whereNull('parent_id')->with('children')->select('id', 'name', 'parent_id')->get();
        $products = \App\Models\Product::select('id', 'name')->get();
        return view('admin.promos.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:promo_codes,code|max:50',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'target_type' => 'required|in:all,category,product',
            'target_ids' => 'nullable|array',
            'target_ids.*' => 'numeric',
        ]);

        PromoCode::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_amount' => $request->min_order_amount,
            'max_discount_amount' => $request->max_discount_amount,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'is_active' => $request->has('is_active'),
            'target_type' => $request->target_type,
            'target_ids' => $request->target_ids ?? [],
        ]);

        return redirect()->route('admin.promos.index')->with('success', 'Promo Code created successfully.');
    }

    public function edit(PromoCode $promo)
    {
        $categories = \App\Models\Category::whereNull('parent_id')->with('children')->select('id', 'name', 'parent_id')->get();
        $products = \App\Models\Product::select('id', 'name')->get();
        return view('admin.promos.edit', compact('promo', 'categories', 'products'));
    }

    public function update(Request $request, PromoCode $promo)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code,' . $promo->id,
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'target_type' => 'required|in:all,category,product',
            'target_ids' => 'nullable|array',
            'target_ids.*' => 'numeric',
        ]);

        $promo->update([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_amount' => $request->min_order_amount,
            'max_discount_amount' => $request->max_discount_amount,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'is_active' => $request->has('is_active'),
            'target_type' => $request->target_type,
            'target_ids' => $request->target_ids ?? [],
        ]);

        return redirect()->route('admin.promos.index')->with('success', 'Promo Code updated successfully.');
    }
    public function destroy(PromoCode $promo)
    {
        $promo->delete();
        return redirect()->route('admin.promos.index')->with('success', 'Promo Code deleted.');
    }
}
