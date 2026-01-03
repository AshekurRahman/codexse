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
        Schema::create('wallet_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('idempotency_key', 64)->unique();
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2);
            $table->string('status')->default('pending'); // pending, captured, released, expired
            $table->string('holdable_type')->nullable();
            $table->unsignedBigInteger('holdable_id')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->foreignId('captured_transaction_id')->nullable()->constrained('wallet_transactions')->onDelete('set null');
            $table->timestamps();

            $table->index(['wallet_id', 'status']);
            $table->index(['status', 'expires_at']);
            $table->index(['holdable_type', 'holdable_id'], 'wallet_holds_holdable_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_holds');
    }
};
