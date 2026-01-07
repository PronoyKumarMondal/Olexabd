<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoCompleteOrders extends Command
{
    protected $signature = 'orders:auto-complete';
    protected $description = 'Automatically define orders as completed 3 days after delivery';

    public function handle()
    {
        $cutoffDate = now()->subDays(3);
        
        $count = \App\Models\Order::where('status', 'delivered')
                    ->where('updated_at', '<=', $cutoffDate)
                    ->update(['status' => 'completed']);
        
        $this->info("Successfully marked {$count} orders as completed.");
    }
}
