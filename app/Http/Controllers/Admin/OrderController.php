<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        // Professional Filters
        if ($request->filled('order_id')) {
            $query->where('order_code', 'like', '%' . $request->order_id . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(8);
        return view('admin.orders.index', compact('orders'));
    }
    
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        // Simple view for logic, in real view we might skip or use modal
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', "Order #{$order->id} status updated to " . ucfirst($request->status));
    }
}
