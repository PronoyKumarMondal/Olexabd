<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        // Permission check
        $admin = auth('admin')->user();
        if (!$admin->isSuperAdmin() && !$admin->hasPermission('manage_settings')) {
            abort(403);
        }

        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
         // Permission check
        $admin = auth('admin')->user();
        if (!$admin->isSuperAdmin() && !$admin->hasPermission('manage_settings')) {
            abort(403);
        }

        $request->validate([
            'delivery_charge_inside_dhaka' => 'required|numeric|min:0',
            'delivery_charge_outside_dhaka' => 'required|numeric|min:0',
        ]);

        Setting::set('delivery_charge_inside_dhaka', $request->delivery_charge_inside_dhaka);
        Setting::set('delivery_charge_outside_dhaka', $request->delivery_charge_outside_dhaka);

        return back()->with('success', 'Settings updated successfully.');
    }
}
