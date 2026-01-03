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
        Schema::create('wallet_idempotency_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->string('operation'); // purchase, hold, release, capture, refund
            $table->foreignId('transaction_id')->nullable()->constrained('wallet_transactions')->onDelete('set null');
            $table->foreignId('hold_id')->nullable()->constrained('wallet_holds')->onDelete('set null');
            $table->json('request_hash')->nullable();
            $table->json('response')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['key', 'wallet_id']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_idempotency_keys');
    }
};
