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
                "price" => $product->price,
                "image" => $product->image
            ];
        }

        session()->put('cart', $cart);
        
        // Return JSON for AJAX or Redirect back
        if ($request->wantsJson()) {
            return response()->json(['success' => 'Product added to cart successfully!', 'count' => count($session->get('cart'))]);
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
            }
            session()->flash('success', 'Product removed successfully');
        }
    }
}
