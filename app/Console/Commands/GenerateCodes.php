<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class GenerateCodes extends Command
{
    protected $signature = 'app:generate-codes';
    protected $description = 'Generate unique codes for existing data';

    public function handle()
    {
        // 1. Categories
        $categories = Category::whereNull('code')->get();
        foreach ($categories as $cat) {
            $cat->update(['code' => strtoupper(substr(md5(uniqid()), 0, 6))]);
        }
        $this->info("Updated {$categories->count()} Categories.");

        // 2. Products
        $products = Product::whereNull('code')->get();
        foreach ($products as $prod) {
            $prod->update(['code' => strtoupper(substr(md5(uniqid()), 0, 6))]);
        }
        $this->info("Updated {$products->count()} Products.");

        // 3. Orders (Update all to new format)
        $orders = Order::all(); // Update ALL orders to ensure consistent format
        foreach ($orders as $order) {
            // Only update if it looks like the old format (contains dash) or is null
            if (str_contains($order->order_code, '-') || is_null($order->order_code)) {
                $order->update([
                    'order_code' => strtoupper(substr(md5(uniqid()), 0, 6)),
                    'source' => $order->source ?? 'web'
                ]);
            }
        }
        $this->info("Updated Orders to new short code format.");
    }
}
