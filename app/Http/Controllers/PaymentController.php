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
        // Calculate Total
        $subtotal = 0;
        foreach($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Apply Coupon
        $discount = 0;
        $couponCode = null;
        if (session()->has('coupon')) {
             $coupon = session('coupon');
             // Recalculate discount based on current total (in case cart changed)
             // Or rely on session value if we trust it won't drift too much without re-validation
             // Ideally re-validate here, but for simplicity read session
             $discount = $coupon['amount'];
             $couponCode = $coupon['code'];
             
             // Ensure discount doesn't exceed total
             if($discount > $subtotal) $discount = $subtotal;
        }

        $total = $subtotal - $discount;

        // Create Order
        $order = Order::create([
            'user_id' => auth()->id(), // Ensure user is logged in
            'total_amount' => $total,
            'coupon_code' => $couponCode,
            'discount_amount' => $discount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'shipping_address' => 'Dhaka, Bangladesh', // Placeholder until address form is added
            'source' => 'web', 
            'traffic_source' => session('order_source'),
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
                 
                 // Persistent Cart Logic: Mark items as sold
                 if (auth()->check()) {
                     \App\Models\CartItem::where('user_id', auth()->id())
                         ->where('status', 'active')
                         ->update(['status' => 'sold']);
                 }

                 // Clear Cart
                 session()->forget('cart');
                 session()->forget('coupon'); // Also clear coupon
                 
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
