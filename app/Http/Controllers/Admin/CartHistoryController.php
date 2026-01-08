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

        $items = CartItem::with(['user', 'product'])->latest()->paginate(10);

        return view('admin.cart_history.index', compact('items'));
    }
}
