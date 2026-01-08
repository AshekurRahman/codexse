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
        Schema::create('processed_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50)->index(); // stripe, payoneer, etc.
            $table->string('event_id', 255); // Unique event identifier
            $table->string('event_type', 100)->nullable(); // payment_intent.succeeded, etc.
            $table->string('idempotency_key', 64)->unique(); // Hash of provider+event_id
            $table->timestamp('event_timestamp')->nullable(); // Original event timestamp
            $table->string('status', 20)->default('processed'); // processed, failed
            $table->json('metadata')->nullable(); // Additional event data
            $table->ipAddress('source_ip')->nullable();
            $table->timestamps();

            // Composite index for efficient lookups
            $table->index(['provider', 'event_id']);

            // Index for cleanup of old records
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processed_webhooks');
    }
};
