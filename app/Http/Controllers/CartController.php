<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // Show Cart Page
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }
        return view('shop.cart', compact('cart', 'total'));
    }

    // Add to Cart
    public function addToCart(Request $request)
    {
        $id = $request->product_id;
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->effective_price, // Use discounted price if active
                "image" => $product->image
            ];
        }

        session()->put('cart', $cart);

        // Persistent Cart Logic
        if (auth()->check()) {
            $existingItem = \App\Models\CartItem::where('user_id', auth()->id())
                ->where('product_id', $id)
                ->where('status', 'active')
                ->first();

            if ($existingItem) {
                $existingItem->increment('quantity');
                $existingItem->update(['price' => $product->effective_price]); // Update price snapshot? Optional.
            } else {
                \App\Models\CartItem::create([
                    'user_id' => auth()->id(),
                    'product_id' => $id,
                    'quantity' => 1,
                    'price' => $product->effective_price,
                    'status' => 'active'
                ]);
            }
        }
        
        // Return JSON for AJAX or Redirect back
        if ($request->wantsJson()) {
            return response()->json(['success' => 'Product added to cart successfully!', 'count' => count(session()->get('cart'))]);
        }

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    // Update Cart
    public function updateCart(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);

            // Persistent Cart Logic
            if (auth()->check()) {
                \App\Models\CartItem::where('user_id', auth()->id())
                    ->where('product_id', $request->id)
                    ->where('status', 'active')
                    ->update(['quantity' => $request->quantity]);
            }

            session()->flash('success', 'Cart updated successfully');
        }
    }

    // Remove from Cart
    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);

                // Persistent Cart Logic
                if (auth()->check()) {
                    \App\Models\CartItem::where('user_id', auth()->id())
                        ->where('product_id', $request->id)
                        ->where('status', 'active')
                        ->update(['status' => 'removed']);
                }
            }
            session()->flash('success', 'Product removed successfully');
        }
    }

    public function applyPromo(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $code = trim(strtoupper($request->code));
        $promo = \App\Models\PromoCode::where('code', $code)->first();

        if (!$promo) {
            return back()->with('error', 'Invalid promo code.');
        }

        // 1. Basic Validation (Active, Dates, Min Amount)
        // Note: Min Amount check should probably be against the "Eligible Total" or "Grand Total"?
        // Usually Min Order Amount applies to the whole cart total.
        if (!$promo->isValid($this->getCartTotal())) {
             return back()->with('error', 'Promo code cannot be applied (Check requirements).');
        }

        // 2. Target Validation & Calculation
        $cart = session('cart', []);
        $eligibleAmount = 0;
        $hasEligibleItem = false;

        if ($promo->target_type == 'all') {
            $eligibleAmount = $this->getCartTotal();
            $hasEligibleItem = true;
        } else {
            // Need to check specific items
            // Fetch necessary product details to check categories if needed
            $productIds = array_keys($cart);
            $products = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($cart as $id => $item) {
                $isItemEligible = false;
                $product = $products[$id] ?? null;

                if (!$product) continue;

                if ($promo->target_type == 'product') {
                    if (in_array($id, $promo->target_ids ?? [])) {
                        $isItemEligible = true;
                    }
                } elseif ($promo->target_type == 'category') {
                    if (in_array($product->category_id, $promo->target_ids ?? [])) {
                        $isItemEligible = true;
                    }
                    // Handle Subcategories logic (if product's category is child of target)
                    // This is complex without loading all categories. 
                    // Optimization: We loaded categories in Admin, but here we just check ID.
                    // If user selected Parent Category, we expect standard "exact match" 
                    // unless we do a recursive check. 
                    // For now, let's Stick to DIRECT Category Match for simplicity and speed.
                    // If user wants to target subcategory, they select it.
                    // Refinment: If we want Parent to include Children, we need to fetch category ancestry.
                    // Let's rely on checking `category_id`.
                }

                if ($isItemEligible) {
                    $eligibleAmount += $item['price'] * $item['quantity'];
                    $hasEligibleItem = true;
                }
            }
        }

        if (!$hasEligibleItem) {
            return back()->with('error', 'This promo code applies to specific items that are not in your cart.');
        }

        // Calculate Discount
        $discountAmount = 0;
        if ($promo->type == 'fixed') {
             // Fixed amount is applied, but capped at eligible amount
             $discountAmount = min($promo->value, $eligibleAmount);
        } else {
             $calculatedDiscount = ($eligibleAmount * $promo->value) / 100;
             if ($promo->max_discount_amount > 0) {
                 $discountAmount = min($calculatedDiscount, $promo->max_discount_amount);
             } else {
                 $discountAmount = $calculatedDiscount;
             }
        }

        session()->put('coupon', [
            'code' => $promo->code,
            'amount' => $discountAmount,
            'type' => $promo->type,
            'value' => $promo->value,
            'target_type' => $promo->target_type,
            'max_discount_amount' => $promo->max_discount_amount // Store for reference display if needed
        ]);

        return back()->with('success', 'Coupon applied successfully!');
    }

    public function removePromo()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed.');
    }

    private function getCartTotal()
    {
        $cart = session('cart', []);
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
}
