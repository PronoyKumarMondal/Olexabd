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
        // Use raw SQL to bypass SQLite table rebuild issues with broken indexes
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE products ADD COLUMN sub_category_id INTEGER NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SQLite supports DROP COLUMN in newer versions
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE products DROP COLUMN sub_category_id');
    }
};
