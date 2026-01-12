<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Channel;
use Illuminate\Support\Facades\DB; 

class OrderController extends Controller
{
    public function create()
    {
        $this->authorizeAdmin('order_create');
        
        $channels = Channel::where('is_active', true)->get();
        // Limit products/users for performance in MVP view, ideally this uses AJAX search
        $products = Product::select('id', 'name', 'price', 'code', 'stock')->where('stock', '>', 0)->get();
        $customers = User::where('role', 'customer')->select('id', 'name', 'email', 'phone')->get();
        
        return view('admin.orders.create', compact('channels', 'products', 'customers'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin('order_create');

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'channel_id' => 'nullable|exists:channels,id', // Matches ID in DB, but Order stores 'source' string. Logic below fixes this.
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string',
            'payment_status' => 'required|in:paid,unpaid'
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $orderItems = [];

            // Calculate Totals and Prepare Items
            foreach ($request->products as $item) {
                $product = Product::find($item['id']);
                
                // Check Stock
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $price = $product->price; // Could apply admin override here if requested later
                $subtotal = $price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'total' => $subtotal
                ];
                
                // Decrement Stock
                $product->decrement('stock', $item['quantity']);
            }

            // Determine Attribution
            $media = 'web'; // Admin uses the web portal but we default media to web or admin specific
            $trafficSource = null;
            $orderPortal = 'Admin Portal';

            if ($request->channel_id) {
                // Channel is now conceptually "Media"
                $channel = Channel::find($request->channel_id);
                if ($channel) {
                    $media = $channel->slug; // e.g. 'phone-call', 'whatsapp'
                }
            }

            // Create Order
            $order = Order::create([
                'user_id' => $request->user_id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'media' => $media, // Was source
                'traffic_source' => $trafficSource,
                'order_portal' => $orderPortal,
                // 'updated_by' handled by Observer
            ]);

            // Create Items
            $order->items()->createMany($orderItems);

            DB::commit();

            return redirect()->route('admin.orders.create')->with('success', 'Order created successfully. You can create another one.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating order: ' . $e->getMessage())->withInput();
        }
    }

    private function authorizeAdmin($permission)
    {
        $admin = auth('admin')->user();
        if (!$admin || (!$admin->isSuperAdmin() && !$admin->hasPermission($permission))) {
            abort(403, 'Unauthorized action.');
        }
    }
    public function index(Request $request)
    {
        $this->authorizeAdmin('order_edit'); // Restrict view to those who can manage orders

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
        $this->authorizeAdmin('order_edit');
        // Simple view for logic, in real view we might skip or use modal
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorizeAdmin('order_edit');
        
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', "Order #{$order->id} status updated to " . ucfirst($request->status));
    }
}
