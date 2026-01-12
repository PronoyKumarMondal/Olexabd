<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if (!auth('admin')->user()->hasPermission('view_customers')) {
            abort(403, 'Unauthorized action.');
        }

        $query = User::where('role', 'customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $customers = $query->latest()->paginate(8);

        return view('admin.customers.index', compact('customers'));
    }
    public function create()
    {
        if (!auth('admin')->user()->hasPermission('view_customers')) { // Re-using permission, or could add 'manage_customers'
            abort(403, 'Unauthorized action.');
        }
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        if (!auth('admin')->user()->hasPermission('view_customers')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
        ]);

        $password = \Str::random(8); // Auto-generate 8 char password

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => \Hash::make($password),
            'role' => 'customer',
            'source' => 'web' 
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully.',
                'customer' => $user
            ]);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }
}
