<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = session('cart');
        
        if(!$cart || count($cart) == 0) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty!');
        }

        // Calculate Total
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Create Order
        $order = Order::create([
            'user_id' => auth()->id(), // Ensure user is logged in
            'total_amount' => $total,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'shipping_address' => 'Dhaka, Bangladesh', // Placeholder until address form is added
        ]);

        // Create Order Items
        foreach($cart as $id => $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['price']
            ]);
        }
        
        // Redirect to Mock Page View with DB Order ID
        return redirect()->route('bkash.mock_page', [
            'amount' => $total,
            'order_id' => 'ORD-' . $order->id, // Display ID
            'order_id_db' => $order->id // Actual DB ID for update
        ]);
    }

    public function mockPage(Request $request)
    {
        return view('shop.bkash_mock', [
            'amount' => $request->amount,
            'order_id' => $request->order_id,
            'order_id_db' => $request->order_id_db // Pass it through
        ]);
    }

    public function success(Request $request)
    {
        if($request->order_id_db) {
             $order = Order::find($request->order_id_db);
             if($order) {
                 $order->update([
                     'payment_status' => 'paid',
                     'payment_method' => 'bkash',
                     'status' => 'processing'
                 ]);
                 
                 // Clear Cart
                 session()->forget('cart');
                 
                 return redirect()->route('orders.index')->with('success', 'Payment Successful! Your order has been placed.');
             }
        }
        
        return redirect()->route('shop.index')->with('error', 'Something went wrong.');
    }

    public function cancel()
    {
        return redirect()->route('cart.index')->with('error', 'Payment Cancelled');
    }
}
