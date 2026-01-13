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
        // Products
        $this->addIndexSafe('products', 'is_active');
        $this->addIndexSafe('products', 'is_featured');
        // Slug is already unique (indexed), so skipping
        
        // Users
        // Email is already unique (indexed), so skipping
        $this->addIndexSafe('users', 'phone');

        // Orders
        $this->addIndexSafe('orders', 'code');
        $this->addIndexSafe('orders', 'created_at');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexSafe('products', 'is_active');
        $this->dropIndexSafe('products', 'is_featured');
        // $this->dropIndexSafe('products', 'slug');

        // $this->dropIndexSafe('users', 'email');
        $this->dropIndexSafe('users', 'phone');

        $this->dropIndexSafe('orders', 'code');
        $this->dropIndexSafe('orders', 'created_at');
    }

    protected function addIndexSafe($table, $column)
    {
        $indexName = "{$table}_{$column}_index";
        
        // Check if index exists using raw SQL for maximum compatibility
        // This query works on MySQL which matches the user's environment
        $exists = count(\Illuminate\Support\Facades\DB::select(
            "SHOW INDEXES FROM {$table} WHERE Key_name = ?", 
            [$indexName]
        )) > 0;

        if (!$exists) {
            Schema::table($table, function (Blueprint $table) use ($column, $indexName) {
                $table->index($column, $indexName);
            });
        }
    }

    protected function dropIndexSafe($table, $column)
    {
        $indexName = "{$table}_{$column}_index";
        
        $exists = count(\Illuminate\Support\Facades\DB::select(
            "SHOW INDEXES FROM {$table} WHERE Key_name = ?", 
            [$indexName]
        )) > 0;

        if ($exists) {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }
};
