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

        // Calculate Delivery Charge
        $deliveryCharge = 0;
        $deliveryLocation = $request->input('delivery_location', 'outside'); // Default to outside if not provided
        
        $hasFreeDelivery = false;
        
        // Check for Free Delivery Items (Product or Category level)
        // Optimization: Use whereIn to fetch all product rules at once if needed, 
        // but since we loop cart later, we can do it here or optimizing.
        // Let's optimize by fetching product details with categories.
        $productIds = array_keys($cart);
        $products = \App\Models\Product::whereIn('id', $productIds)->with('category')->get();
        
        foreach($products as $product) {
            if ($product->is_free_delivery) {
                $hasFreeDelivery = true;
                break;
            }
            if ($product->category && $product->category->is_free_delivery) {
                $hasFreeDelivery = true;
                break;
            }
        }
        
        if (!$hasFreeDelivery) {
            if ($deliveryLocation === 'inside') {
                $deliveryCharge = \App\Models\Setting::get('delivery_charge_inside_dhaka', 60);
            } else {
                $deliveryCharge = \App\Models\Setting::get('delivery_charge_outside_dhaka', 120);
            }
        }

        $total = $subtotal + $deliveryCharge - $discount;

        // Create Order
        $order = Order::create([
            'user_id' => auth()->id(), // Ensure user is logged in
            'total_amount' => $total,
            'delivery_charge' => $deliveryCharge,
            'coupon_code' => $couponCode,
            'discount_amount' => $discount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'shipping_address' => 'Dhaka, Bangladesh', // Placeholder until address form is added
            'shipping_address' => 'Dhaka, Bangladesh', // Placeholder until address form is added
            'media' => session('order_platform', 'web'), // Default to 'web' if no platform detected
            'traffic_source' => session('order_campaign'), // Nullable
            'order_portal' => 'Customer Portal - Web',
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
                 
                 // Trigger Facebook CAPI Purchase Event
                 try {
                     $fbMeta = new \App\Services\FacebookMeta();
                     $fbMeta->sendPurchaseEvent($order);
                 } catch (\Exception $e) {
                     // Do not fail the transaction if tracking fails
                 }

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
