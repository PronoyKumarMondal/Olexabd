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
        // Users Table
        Schema::table('users', function (Blueprint $table) {
            $table->string('source')->default('web')->after('email'); // web, app, fb_app
        });

        // Categories Table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('code', 10)->unique()->nullable()->after('id');
        });

        // Products Table
        Schema::table('products', function (Blueprint $table) {
            $table->string('code', 10)->unique()->nullable()->after('id');
        });

        // Orders Table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_code', 12)->unique()->nullable()->after('id');
            $table->string('source')->default('web')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['source']);
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['code']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['code']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_code', 'source']);
        });
    }
};
