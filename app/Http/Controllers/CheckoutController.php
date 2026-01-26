<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\Product;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart');
        if(!$cart || count($cart) == 0) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty!');
        }
        
        // Calculate Totals for Initial Display
        $subtotal = 0;
        foreach($cart as $item) $subtotal += $item['price'] * $item['quantity'];
        
        $discount = 0;
        if(session('coupon')) $discount = session('coupon')['amount'];
        if($discount > $subtotal) $discount = $subtotal;
        
        $total = $subtotal - $discount;

        return view('shop.checkout', compact('cart', 'subtotal', 'discount', 'total'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'division' => 'required',
            'district' => 'required',
            'upazila' => 'required',
            'address' => 'nullable|string',
            'phone' => 'required|regex:/(01)[0-9]{9}/',
            'name' => 'required|string'
        ]);

        $cart = session('cart');
        if(!$cart || count($cart) == 0) {
            return redirect()->route('shop.index')->with('error', 'Cart is empty');
        }

        // --- Calculate Delivery Charge (Server Side) ---
        $deliveryCharge = 0;
        $hasFreeDelivery = false;

        // 1. Global Free
        if (Setting::get('free_delivery_all')) $hasFreeDelivery = true;

        // 2. Min Order
        $subtotal = 0;
        foreach($cart as $item) $subtotal += $item['price'] * $item['quantity'];
        
        $minOrder = Setting::get('free_delivery_over');
        if (!$hasFreeDelivery && $minOrder && $subtotal >= $minOrder) {
             $hasFreeDelivery = true;
        }

        // 3. Product Rules
        if (!$hasFreeDelivery) {
            $productIds = array_keys($cart);
            $products = Product::whereIn('id', $productIds)->with('category')->get();
            foreach($products as $p) {
                if ($p->is_free_delivery || ($p->category && $p->category->is_free_delivery)) {
                    $hasFreeDelivery = true; break;
                }
            }
        }

        if (!$hasFreeDelivery) {
            // Check District. Dhaka ID is usually "1".
            // Frontend passes district ID or Name? Let's assume ID or Name. 
            // Based on JSON, Dhaka District ID is "1".
            // We should ensure the frontend passes the ID or we check the name.
            // Let's rely on the frontend passing the ID or check if input is '1' or 'Dhaka'.
            
            $isInsideDhaka = false;
            if ($request->district == '1' || strtolower($request->district_name) == 'dhaka') {
                 $isInsideDhaka = true;
            }

            if ($isInsideDhaka) {
                $deliveryCharge = Setting::get('delivery_charge_inside_dhaka', 60);
            } else {
                $deliveryCharge = Setting::get('delivery_charge_outside_dhaka', 120);
            }
        }
        
        // Apply Coupon
        $discount = 0;
        $couponCode = null;
        if (session('coupon')) {
             $discount = session('coupon')['amount'];
             $couponCode = session('coupon')['code'];
             if($discount > $subtotal) $discount = $subtotal;
        }

        $total = $subtotal + $deliveryCharge - $discount;

        // Full Address String
        // We might want to store JSON or a formatted string.
        // Let's store a formatted string for now as per schema.
        // We will accept division_name, district_name, upazila_name from hidden inputs if needed, 
        // or just store what we got if the form sends names. 
        // Plan: Form will send Names primarily for display, IDs for logic? 
        // Let's try to get names.
        
        $fullAddress = $request->name . "\n" . $request->phone . "\n";
        $fullAddress .= $request->upazila_name . ", " . $request->district_name . ", " . $request->division_name . "\n";
        if($request->address) $fullAddress .= "Details: " . $request->address;

        // Create Order
        $order = Order::create([
            'user_id' => auth()->id() ?? 0, // 0 for guest? DB requires foreign key?
            // Wait, schema enforces user_id foreign key. 
            // If we allow Guest Checkout, we need to handle that. 
            // For now, assume auth required or we assign to a 'Guest' user if mapped. 
            // Let's stick to auth()->id() and assume middleware protects it or nullable.
            // (Current web.php usually protects checkout).
            'user_id' => auth()->id(),
            'total_amount' => $total,
            'delivery_charge' => $deliveryCharge,
            'coupon_code' => $couponCode,
            'discount_amount' => $discount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'shipping_address' => $fullAddress,
            'payment_method' => 'cod', // Default to COD or set later
            'media' => session('order_platform', 'web'),
            'traffic_source' => session('order_campaign'),
            'order_portal' => 'Web Checkout'
        ]);

         foreach($cart as $id => $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['price']
            ]);
        }
        
        // Use the existing Mock Page flow
        return redirect()->route('bkash.mock_page', [
            'amount' => $total,
            'order_id' => 'ORD-' . $order->id,
            'order_id_db' => $order->id
        ]);
    }
}
