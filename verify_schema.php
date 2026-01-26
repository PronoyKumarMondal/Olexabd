<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hasTable = Schema::hasTable('divisions');
$hasCol = Schema::hasColumn('orders', 'delivery_division_id');
$rowCount = $hasTable ? DB::table('divisions')->count() : 0;
$upazilaCount = Schema::hasTable('upazilas') ? DB::table('upazilas')->count() : 0;

echo json_encode([
    'divisions_table_exists' => $hasTable,
    'orders_has_column' => $hasCol,
    'divisions_count' => $rowCount,
    'upazilas_count' => $upazilaCount
]);
