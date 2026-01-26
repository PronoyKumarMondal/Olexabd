<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Disable FK Checks
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Drop Tables
$tables = ['postcodes', 'upazilas', 'districts', 'divisions'];
foreach ($tables as $table) {
    Schema::dropIfExists($table);
    echo "Dropped $table.\n";
}

// Drop Columns from Orders
$columns = ['delivery_division_id', 'delivery_district_id', 'delivery_upazila_id', 'delivery_postcode', 'delivery_address', 'delivery_phone'];
if (Schema::hasTable('orders')) {
    Schema::table('orders', function ($table) use ($columns) {
        foreach ($columns as $col) {
            if (Schema::hasColumn('orders', $col)) {
                // Try dropping FK first
                try {
                    $fkName = "orders_{$col}_foreign";
                    $table->dropForeign($fkName);
                } catch (\Exception $e) {
                     // Try array notation
                     try {
                        $table->dropForeign([$col]);
                     } catch(\Exception $e2) {}
                }
                
                // Drop Column
                try {
                    $table->dropColumn($col);
                    echo "Dropped column $col from orders.\n";
                } catch (\Exception $e) {
                    echo "Failed to drop $col: " . $e->getMessage() . "\n";
                }
            }
        }
    });
}

// Enable FK Checks
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// Clear Migration entries for these files
DB::table('migrations')->where('migration', 'like', '%create_locations_tables%')->delete();
DB::table('migrations')->where('migration', 'like', '%add_details_to_orders_table%')->delete();

echo "Cleanup complete.\n";
