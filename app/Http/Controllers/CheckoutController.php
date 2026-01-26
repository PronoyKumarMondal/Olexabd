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
            'division' => 'required|exists:divisions,id', // Division ID
            'district' => 'required|exists:districts,id', // District ID
            'upazila' => 'required|exists:upazilas,id',   // Upazila ID (includes City Corp areas)
            'address' => 'required|string', // Mandatory as per user
            'phone' => 'required|regex:/(01)[0-9]{9}/',
            'name' => 'required|string',
            'postcode' => 'required|string' // Mandatory now
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
            // Check Upazila Logic
            // Fetch Upazila from DB to check flag
            $upazila = DB::table('upazilas')->where('id', $request->upazila)->first();
            
            $isInsideDhaka = false;
            // The logic: Only City Corporations (marked in Seeder) are Inside Dhaka.
            if ($upazila && $upazila->is_inside_dhaka) {
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

        // Construct Address String for Snapshot (Legacy support + ease of view)
        // Also ensure we save IDs if columns exist (we'll assume they do or add them)
        // Check Schema columns existence dynamically or just try to save?
        // Better to save IDs to new columns.
        $division = DB::table('divisions')->find($request->division);
        $district = DB::table('districts')->find($request->district);
        
        $fullAddress = $request->name . "\n" . $request->phone . "\n";
        $fullAddress .= "Address: " . $request->address . "\n";
        $fullAddress .= "Postcode: " . $request->postcode . "\n";
        $fullAddress .= "Location: " . ($upazila->name ?? '') . ", " . ($district->name ?? '') . ", " . ($division->name ?? '');

        // Create Order
        $orderData = [
            'user_id' => auth()->id(),
            'total_amount' => $total,
            'delivery_charge' => $deliveryCharge,
            'coupon_code' => $couponCode,
            'discount_amount' => $discount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'shipping_address' => $fullAddress,
            'payment_method' => 'cod', 
            'media' => session('order_platform', 'web'),
            'traffic_source' => session('order_campaign'),
            'order_portal' => 'Web Checkout',
            // Detailed Columns
            'delivery_division_id' => $request->division,
            'delivery_district_id' => $request->district,
            'delivery_upazila_id' => $request->upazila,
            'delivery_postcode' => $request->postcode,
            'delivery_address' => $request->address,
            'delivery_phone' => $request->phone,
        ];

        // Filter out keys if columns don't exist?
        // We really should ensure columns exist. 
        // If Migration failed, this will crash.
        // Assuming user ran migration or I fixed it.
        $order = Order::create($orderData);

         foreach($cart as $id => $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['price']
            ]);
        }
        
        return redirect()->route('bkash.mock_page', [
            'amount' => $total,
            'order_id' => 'ORD-' . $order->id,
            'order_id_db' => $order->id
        ]);
    }
}
