<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\Product;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $mode = $request->query('mode', 'cart');
        
        if ($mode == 'buy_now') {
            $cart = session('direct_checkout_item');
        } else {
            $cart = session('cart');
        }

        if(!$cart || count($cart) == 0) {
            // Fallback: If buy now session expired or manipulated, check main cart
            if ($mode == 'buy_now' && session()->has('cart') && count(session('cart')) > 0) {
                return redirect()->route('checkout.page'); // Redirect to normal checkout
            }
            return redirect()->route('shop.index')->with('error', 'Your cart is empty!');
        }
        
        // Calculate Totals for Initial Display
        $subtotal = 0;
        foreach($cart as $item) $subtotal += $item['price'] * $item['quantity'];
        
        $discount = 0;
        if(session('coupon')) $discount = session('coupon')['amount'];
        if($discount > $subtotal) $discount = $subtotal;
        
        $total = $subtotal - $discount;

        return view('shop.checkout', compact('cart', 'subtotal', 'discount', 'total', 'mode'));
    }

    public function placeOrder(Request $request)
    {
        // Initial Validation
        $request->validate([
            'division' => 'required|exists:divisions,id',
            'district' => 'required|exists:districts,id',
            'upazila' => 'required|exists:upazilas,id',
            'address' => 'required|string',
            'phone' => 'required|regex:/(01)[0-9]{9}/',
            'name' => 'required|string',
            'postcode' => 'required|string',
            'payment_method' => 'required|in:cod,bkash,bank',
            // Conditionals handled below or via complex rule
        ]);

        // ... delivery calculation ... (keep existing code for safe keeping, referencing logical flow)


        $mode = $request->input('mode', 'cart');
        if ($mode == 'buy_now') {
            $cart = session('direct_checkout_item');
        } else {
            $cart = session('cart');
        }

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

        // ... existing delivery calculation logic ...

        $total = $subtotal + $deliveryCharge - $discount;

        // Custom Validation for Manual Payment
        $paymentMethod = $request->input('payment_method');
        $trxId = $request->input('transaction_id');
        $paymentNumber = $request->input('payment_number');

        if ($paymentMethod == 'cod') {
             // For COD, if delivery charge > 0, we need Advance Payment Logic
             if ($deliveryCharge > 0) {
                 if (empty($trxId) || empty($paymentNumber)) {
                     return back()->withInput()->with('error', 'For COD outside free delivery, please pay the delivery charge in advance and provide TrxID.');
                 }
             }
        } elseif ($paymentMethod == 'bkash' || $paymentMethod == 'bank') {
             // Full Payment Required
             if (empty($trxId) || empty($paymentNumber)) {
                 return back()->withInput()->with('error', 'Please complete the payment and provide Transaction ID & Sender Number.');
             }
        }

        // ... address construction ...
        $division = DB::table('divisions')->find($request->division);
        $district = DB::table('districts')->find($request->district);
        
        $fullAddress = $request->name . "\n" . $request->phone . "\n";
        $fullAddress .= "Address: " . $request->address . "\n";
        $fullAddress .= "Postcode: " . $request->postcode . "\n";
        $fullAddress .= "Location: " . ($upazila->name ?? '') . ", " . ($district->name ?? '') . ", " . ($division->name ?? '');

        // Determine Payment Status
        $paymentStatus = 'unpaid';
        // Note: Even if they paid, we mark it 'unpaid' until Admin verifies.
        // Or we could have a 'reviewing' status if DB supports it. 
        // Keeping 'unpaid' is safer as requested.

        // Create Order
        $orderData = [
            'user_id' => auth()->id(),
            'total_amount' => $total,
            'delivery_charge' => $deliveryCharge,
            'coupon_code' => $couponCode,
            'discount_amount' => $discount,
            'status' => 'pending',
            'payment_status' => $paymentStatus,
            'shipping_address' => $fullAddress,
            'payment_method' => $paymentMethod,  // Dynamic
            'transaction_id' => $trxId,          // New
            'payment_number' => $paymentNumber,  // New
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
        
        // Clear appropriate session
        if ($mode == 'buy_now') {
            session()->forget('direct_checkout_item');
        } else {
            session()->forget('cart');
            // Also update persistent logic? 
            // If user cleared main cart, we mark DB items as ordered/inactive?
            // Usually we mark them as 'ordered' if we track that. 
            // For now, removing session is key.
            if(auth()->check()) {
                \App\Models\CartItem::where('user_id', auth()->id())->where('status', 'active')->update(['status' => 'ordered']);
            }
        }
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order placed successfully! Please wait for admin verification.');
    }
}
