<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$migrationName = '2026_02_05_171101_add_sub_category_id_to_products_table';

$exists = DB::table('migrations')->where('migration', $migrationName)->exists();

if (!$exists) {
    DB::table('migrations')->insert([
        'migration' => $migrationName,
        'batch' => DB::table('migrations')->max('batch') + 1
    ]);
    echo "Migration '$migrationName' manually marked as run.\n";
} else {
    echo "Migration '$migrationName' was already marked as run.\n";
}
