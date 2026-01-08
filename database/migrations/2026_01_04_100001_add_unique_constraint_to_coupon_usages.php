<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a unique constraint to prevent the same coupon from being used
     * multiple times on the same order (race condition protection).
     */
    public function up(): void
    {
        Schema::table('coupon_usages', function (Blueprint $table) {
            // Prevent duplicate coupon usage per order
            $table->unique(['coupon_id', 'order_id'], 'coupon_usages_coupon_order_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupon_usages', function (Blueprint $table) {
            $table->dropUnique('coupon_usages_coupon_order_unique');
        });
    }
};
