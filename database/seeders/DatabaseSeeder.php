<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\AdminSeeder; // Added this line

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Users
        $this->call([
            AdminSeeder::class,
        ]);

        // The AdminSeeder will handle the creation of the admin user.
        // The following code for admin user creation is removed as per instruction.
        // $admin = User::firstOrCreate(
        //     ['email' => 'admin@olexabd.com'],
        //     [
        //         'name' => 'Admin User',
        //         'password' => Hash::make('password'),
        //         'is_admin' => true,
        //     ]
        // );

        $customer = User::firstOrCreate(
            ['email' => 'customer@olexabd.com'],
            [
                'name' => 'Demo Customer',
                'password' => Hash::make('password'),
                'is_admin' => false, // Explicitly not admin
            ]
        );

        // 2. Create Categories
        $kitchen = Category::firstOrCreate(
            ['slug' => 'kitchen-appliances'],
            ['name' => 'Kitchen Appliances', 'is_active' => true]
        );
        
        $home = Category::firstOrCreate(
            ['slug' => 'home-comfort'],
            ['name' => 'Home Comfort', 'is_active' => true]
        );

        // 3. Create Products
        Product::firstOrCreate(
            ['name' => 'Smart Blender 3000'],
            [
                'slug' => 'smart-blender-3000',
                'category_id' => $kitchen->id,
                'description' => 'High-speed blender with smart presets for smoothies and soups.',
                'price' => 150.00,
                'stock' => 50,
                'image' => 'https://images.unsplash.com/photo-1570222094114-28a9d88a2a74?w=500&q=80',
                'is_active' => true,
                'is_featured' => true
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Air Purifier Pro'],
            [
                'slug' => 'air-purifier-pro',
                'category_id' => $home->id,
                'description' => 'HEPA filter air purifier for rooms up to 500 sq ft.',
                'price' => 299.99,
                'stock' => 30,
                'image' => 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=500&q=80',
                'is_active' => true,
                'is_featured' => true
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Microwave Oven X1'],
            [
                'slug' => 'microwave-oven-x1',
                'category_id' => $kitchen->id,
                'description' => 'Compact 900W microwave with grill function.',
                'price' => 120.50,
                'stock' => 15,
                'image' => 'https://images.unsplash.com/photo-1584269600519-112d071b35e6?w=500&q=80',
                'is_active' => true,
                'is_featured' => false
            ]
        );

        // 4. Create Demo Orders
        $products = Product::all();
        
        // Order 1: Completed
        $order1 = \App\Models\Order::create([
            'user_id' => $customer->id,
            'total_amount' => 449.99,
            'status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'bkash',
            'shipping_address' => 'House 12, Road 5, Dhanmondi, Dhaka'
        ]);
        
        \App\Models\OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $products[0]->id, // Blender
            'quantity' => 1,
            'unit_price' => $products[0]->price
        ]);
        \App\Models\OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $products[1]->id, // Air Purifier
            'quantity' => 1,
            'unit_price' => $products[1]->price
        ]);

        // Order 2: Pending
        $order2 = \App\Models\Order::create([
            'user_id' => $customer->id,
            'total_amount' => 120.50,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'bkash',
            'shipping_address' => 'Flat 4B, Gulshan Avenue, Dhaka'
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $products[2]->id, // Microwave
            'quantity' => 1,
            'unit_price' => $products[2]->price
        ]);
        
        // Order 3: Processing
        $order3 = \App\Models\Order::create([
            'user_id' => $customer->id,
            'total_amount' => 300.00,
            'status' => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'bkash',
            'shipping_address' => 'Uttara Sector 7, Dhaka'
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $products[0]->id, 
            'quantity' => 2,
            'unit_price' => $products[0]->price
        ]);
        
        $this->command->info('Demo Data Seeded Successfully!');
        $this->command->info('Admin: admin@olexabd.com | password');
        $this->command->info('Customer: customer@olexabd.com | password');
    }
}
