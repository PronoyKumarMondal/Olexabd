<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    // Drop Tables directly
    DB::statement('DROP TABLE IF EXISTS postcodes');
    DB::statement('DROP TABLE IF EXISTS upazilas');
    DB::statement('DROP TABLE IF EXISTS districts');
    DB::statement('DROP TABLE IF EXISTS divisions');
    echo "Dropped tables.\n";

    // Drop Columns from Orders
    $columns = ['delivery_phone', 'delivery_address', 'delivery_postcode', 'delivery_upazila_id', 'delivery_district_id', 'delivery_division_id'];
    
    // Find FK Names via Schema 
    // This is safer than guessing
    $fksToCheck = [];
    foreach($columns as $col) {
        // Query param matching
        $fks = DB::select(
            "SELECT CONSTRAINT_NAME 
             FROM information_schema.KEY_COLUMN_USAGE 
             WHERE TABLE_SCHEMA = ? 
             AND TABLE_NAME = 'orders' 
             AND COLUMN_NAME = ?", 
            [env('DB_DATABASE', 'u952568757_olexabd'), $col]
        );
        foreach($fks as $fk) {
            $fksToCheck[] = $fk->CONSTRAINT_NAME;
        }
    }

    foreach($fksToCheck as $fkName) {
        try {
            DB::statement("ALTER TABLE orders DROP FOREIGN KEY `{$fkName}`");
            echo "Dropped FK {$fkName}\n";
        } catch (\Exception $e) {   }
    }

    foreach ($columns as $col) {
        try {
            DB::statement("ALTER TABLE orders DROP COLUMN {$col}");
             echo "Dropped column $col\n";
        } catch (\Exception $e) {}
    }
    
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
    // Clear migration table
    DB::table('migrations')->where('migration', 'like', '%create_locations_tables%')->delete();
    DB::table('migrations')->where('migration', 'like', '%add_details_to_orders_table%')->delete();

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
