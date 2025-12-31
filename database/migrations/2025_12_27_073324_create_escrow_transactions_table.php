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
        Schema::create('escrow_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->foreignId('payer_id')->constrained('users')->cascadeOnDelete(); // Buyer/Client
            $table->foreignId('payee_id')->constrained('users')->cascadeOnDelete(); // Seller's user_id
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->morphs('escrowable'); // ServiceOrder, JobMilestone
            $table->decimal('amount', 10, 2);
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('payee_amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'held', 'partial_released', 'released', 'refunded', 'disputed'])->default('pending');
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_transfer_id')->nullable();
            $table->timestamp('held_at')->nullable();
            $table->timestamp('release_requested_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('auto_release_at')->nullable();
            $table->text('dispute_reason')->nullable();
            $table->timestamp('disputed_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['payer_id', 'status']);
            $table->index(['payee_id', 'status']);
            $table->index('auto_release_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escrow_transactions');
    }
};
