<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_division_id')) {
                $table->foreignId('delivery_division_id')->nullable()->constrained('divisions');
            }
            if (!Schema::hasColumn('orders', 'delivery_district_id')) {
                $table->foreignId('delivery_district_id')->nullable()->constrained('districts');
            }
            if (!Schema::hasColumn('orders', 'delivery_upazila_id')) {
                $table->foreignId('delivery_upazila_id')->nullable()->constrained('upazilas');
            }
            if (!Schema::hasColumn('orders', 'delivery_postcode')) {
                $table->string('delivery_postcode')->nullable();
            }
            if (!Schema::hasColumn('orders', 'delivery_address')) {
                $table->text('delivery_address')->nullable();
            }
            if (!Schema::hasColumn('orders', 'delivery_phone')) {
                $table->string('delivery_phone')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
