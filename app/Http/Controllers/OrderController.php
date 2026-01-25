<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        // Ensure user can only see their own orders unless checking via tracking (handled separately or via this with logic)
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Show the tracking form.
     */
    public function track()
    {
        return view('orders.track');
    }

    /**
     * Handle the tracking request.
     */
    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string'
        ]);

        $order = Order::where('order_code', $request->order_code)->first();

        if (!$order) {
            return back()->with('error', 'Order not found. Please check your Order Code.');
        }

        return view('orders.track_result', compact('order'));
    }
}
