<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Attempting to drop index...\n";

try {
    DB::statement('DROP INDEX IF EXISTS "products_featured_index"');
    echo "Index dropped successfully (or didn't exist).\n";
} catch (\Exception $e) {
    echo "Error dropping index: " . $e->getMessage() . "\n";
}

echo "Running migrate...\n";
try {
    Illuminate\Support\Facades\Artisan::call('migrate');
    echo "Migration output:\n" . Illuminate\Support\Facades\Artisan::output() . "\n";
} catch (\Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
