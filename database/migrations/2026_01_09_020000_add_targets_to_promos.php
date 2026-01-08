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
        Schema::table('promo_codes', function (Blueprint $table) {
            // Make dates mandatory (Done via change(), requiring doctrine/dbal, 
            // or just by validation if DB modification is risky on live data without that package.
            // Since this is a new app, we can try modifying or just rely on validation for now 
            // to avoid 'missing dependency' errors common in standard Laravel setups).
            // We will add the NEW columns here.
            
            $table->enum('target_type', ['all', 'category', 'product'])->default('all')->after('value');
            $table->json('target_ids')->nullable()->after('target_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropColumn(['target_type', 'target_ids']);
        });
    }
};
