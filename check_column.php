<?php

use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hasColumn = Schema::hasColumn('products', 'sub_category_id');

if ($hasColumn) {
    echo "COLUMN_EXISTS";
} else {
    echo "COLUMN_MISSING";
}
