<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('escrow_transactions', function (Blueprint $table) {
            // Rename payee_amount to seller_amount for consistency with other models
            $table->renameColumn('payee_amount', 'seller_amount');

            // Add missing columns
            $table->text('notes')->nullable()->after('resolved_at');
            $table->timestamp('refunded_at')->nullable()->after('released_at');
        });

        // Fix status enum to include all required values
        // MySQL doesn't support adding to enum directly via schema builder, so use raw SQL
        // Skip for SQLite as it doesn't support ENUM (uses TEXT instead)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE escrow_transactions MODIFY COLUMN status ENUM('pending', 'held', 'partial_released', 'released', 'refunded', 'partially_refunded', 'disputed', 'cancelled') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('escrow_transactions', function (Blueprint $table) {
            // Rename back
            $table->renameColumn('seller_amount', 'payee_amount');

            // Drop added columns
            $table->dropColumn(['notes', 'refunded_at']);
        });

        // Revert status enum (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE escrow_transactions MODIFY COLUMN status ENUM('pending', 'held', 'partial_released', 'released', 'refunded', 'disputed') DEFAULT 'pending'");
        }
    }
};
