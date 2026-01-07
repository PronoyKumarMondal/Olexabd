<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Categories
        $categories = ['Electronics', 'Home Appliances', 'Kitchenware', 'Personal Care'];
        $categoryIds = [];
        
        foreach ($categories as $catName) {
            $cat = Category::firstOrCreate(
                ['slug' => \Str::slug($catName)],
                [
                    'name' => $catName,
                    'is_active' => true,
                    // Placeholder image
                    'image' => 'https://placehold.co/400x300?text=' . urlencode($catName),
                ]
            );
            $categoryIds[] = $cat->id;
        }

        // 2. Create Products (10 items)
        for ($i = 1; $i <= 10; $i++) {
            Product::create([
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'name' => 'Demo Product ' . $i,
                'slug' => 'demo-product-' . $i . '-' . uniqid(),
                'description' => 'This is a demo description for product ' . $i,
                'price' => rand(100, 2000),
                'stock' => rand(5, 50),
                'is_active' => true,
                'is_featured' => rand(0, 1) == 1,
                'image' => 'https://placehold.co/400x400?text=Product+' . $i,
            ]);
        }

        // 3. Create Customers (5 Users)
        $customerIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => "customer{$i}@example.com"],
                [
                    'name' => "Customer {$i}",
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                    'role' => 'customer' // Assuming you have a role column or using is_admin=false
                ]
            );
            $customerIds[] = $user->id;
        }

        // 4. Create Orders (10 Orders)
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        
        for ($i = 1; $i <= 10; $i++) {
            $order = Order::create([
                'user_id' => $customerIds[array_rand($customerIds)],
                'total_amount' => 0, // Will calculate below
                'status' => $statuses[array_rand($statuses)],
                'payment_status' => rand(0, 1) ? 'paid' : 'unpaid',
                'payment_method' => 'cod',
                'shipping_address' => json_encode(['address' => '123 Demo St, Dhaka']),
                'order_code' => strtoupper(\Str::random(8)),
            ]);

            // Add Items to Order
            $total = 0;
            $products = Product::inRandomOrder()->take(rand(1, 3))->get();
            
            foreach ($products as $product) {
                $qty = rand(1, 2);
                $price = $product->price;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                ]);
                
                $total += ($price * $qty);
            }

            $order->update(['total_amount' => $total]);
        }
        
        $this->command->info('Demo data seeded successfully!');
    }
}
