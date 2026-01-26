<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Schema::disableForeignKeyConstraints();
Schema::dropIfExists('postcodes');
Schema::dropIfExists('upazilas');
Schema::dropIfExists('districts');
Schema::dropIfExists('divisions');

if (Schema::hasColumn('orders', 'delivery_division_id')) {
    Schema::table('orders', function ($table) {
        $table->dropForeign(['delivery_division_id']);
        $table->dropForeign(['delivery_district_id']);
        $table->dropForeign(['delivery_upazila_id']);
        $table->dropColumn(['delivery_division_id', 'delivery_district_id', 'delivery_upazila_id', 'delivery_postcode', 'delivery_address', 'delivery_phone']);
    });
}
Schema::enableForeignKeyConstraints();

echo "Dropped location tables.\n";
