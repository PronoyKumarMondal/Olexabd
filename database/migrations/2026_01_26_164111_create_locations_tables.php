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
        if (!Schema::hasTable('divisions')) {
            Schema::create('divisions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('bn_name')->nullable();
                $table->string('url')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('districts')) {
             Schema::create('districts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('division_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('bn_name')->nullable();
                $table->string('lat')->nullable();
                $table->string('long')->nullable();
                $table->string('url')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('upazilas')) {
            Schema::create('upazilas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('district_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('bn_name')->nullable();
                $table->string('url')->nullable();
                $table->boolean('is_inside_dhaka')->default(false); // Distinction for delivery charge
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('postcodes')) {
            Schema::create('postcodes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('division_id')->constrained()->cascadeOnDelete();
                $table->foreignId('district_id')->constrained()->cascadeOnDelete();
                $table->string('upazila')->nullable(); // Storing as string because JSON has it as string
                $table->string('postOffice')->nullable();
                $table->string('postCode');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postcodes');
        Schema::dropIfExists('upazilas');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('locations_tables'); // Just in case
    }
};
