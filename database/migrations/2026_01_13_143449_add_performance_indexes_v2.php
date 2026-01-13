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
        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('slug');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('phone');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('code'); // order_id
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['slug']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropIndex(['created_at']);
        });
    }
};
