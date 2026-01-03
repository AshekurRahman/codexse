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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('wallet_amount', 12, 2)->nullable()->after('total');
            $table->foreignId('wallet_hold_id')->nullable()->after('wallet_amount')->constrained('wallet_holds')->onDelete('set null');
            $table->foreignId('wallet_transaction_id')->nullable()->after('wallet_hold_id')->constrained('wallet_transactions')->onDelete('set null');
            $table->string('secondary_payment_method')->nullable()->after('payment_method');
            $table->string('secondary_payment_id')->nullable()->after('secondary_payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('wallet_hold_id');
            $table->dropConstrainedForeignId('wallet_transaction_id');
            $table->dropColumn(['wallet_amount', 'secondary_payment_method', 'secondary_payment_id']);
        });
    }
};
