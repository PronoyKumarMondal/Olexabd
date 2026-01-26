<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Clean Process...\n";

// Disable FKs
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// 1. Drop Locations Tables
$tables = ['postcodes', 'upazilas', 'districts', 'divisions'];
foreach($tables as $t) {
    Schema::dropIfExists($t);
    echo "Dropped $t.\n";
}

// 2. Drop Orders Columns (Brute Force)
$cols = ['delivery_division_id', 'delivery_district_id', 'delivery_upazila_id', 'delivery_postcode', 'delivery_address', 'delivery_phone'];
foreach($cols as $col) {
    try {
        // Try dropping FK standard name
        DB::statement("ALTER TABLE orders DROP FOREIGN KEY orders_{$col}_foreign");
    } catch(\Exception $e) {}

    try {
        DB::statement("ALTER TABLE orders DROP COLUMN {$col}");
        echo "Dropped $col.\n";
    } catch(\Exception $e) {}
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// 3. Clear Migrations Table
DB::table('migrations')->where('migration', 'like', '%create_locations_%')->delete();
DB::table('migrations')->where('migration', 'like', '%add_details_to_orders_%')->delete();

echo "Clean Complete. Running Migrate...\n";

try {
    Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
} catch(\Exception $e) {
    echo "Migrate Failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Running Seed...\n";
try {
    Artisan::call('db:seed', ['--class' => 'LocationsSeeder', '--force' => true]);
    echo Artisan::output();
} catch(\Exception $e) {
    echo "Seed Failed: " . $e->getMessage() . "\n";
}

echo "DONE.\n";
