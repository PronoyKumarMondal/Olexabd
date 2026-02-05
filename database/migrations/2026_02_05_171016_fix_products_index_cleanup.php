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
        // Fix for SQLite "General error: 1 near ")": syntax error" due to broken index
        try {
            \Illuminate\Support\Facades\DB::statement('DROP INDEX IF EXISTS "products_featured_index"');
        } catch (\Exception $e) {
            // Ignore if it fails, maybe unrelated
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
