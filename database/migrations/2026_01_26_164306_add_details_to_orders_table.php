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
            $table->foreignId('delivery_division_id')->nullable()->constrained('divisions');
            $table->foreignId('delivery_district_id')->nullable()->constrained('districts');
            $table->foreignId('delivery_upazila_id')->nullable()->constrained('upazilas');
            $table->string('delivery_postcode')->nullable();
            $table->text('delivery_address')->nullable(); // Detailed address (Road, House, etc.)
            $table->string('delivery_phone')->nullable();
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
