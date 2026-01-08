<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;

class CartHistoryController extends Controller
{
    public function index()
    {
        if (!auth('admin')->user()->hasPermission('view_cart_history')) {
            abort(403, 'Unauthorized action.');
        }

        $query = CartItem::with(['user', 'product']);

        // Search Filter (Customer or Product)
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function($p) use ($search) {
                    $p->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Status Filter
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // Date Range Filter
        if ($startDate = request('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = request('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $items = $query->latest()->paginate(10)->withQueryString();

        return view('admin.cart_history.index', compact('items'));
    }
}
