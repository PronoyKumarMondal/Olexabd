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
        Schema::table('users', function (Blueprint $table) {
            // Drop unused admin columns since we have separate 'admins' table
            $table->dropColumn(['permissions', 'is_admin']);
            // keeping 'role' as it distinguishes customer vs potentially other user types, though mostly 'customer'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
            $table->json('permissions')->nullable();
        });
    }
};
