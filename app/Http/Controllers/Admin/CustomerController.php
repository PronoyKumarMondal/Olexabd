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
                  ->orWhere('email', 'like', "%{$search}%");
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
}
