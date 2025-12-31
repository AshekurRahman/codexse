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
        Schema::create('service_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('message_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->json('files')->nullable(); // Delivery files
            $table->enum('status', ['pending', 'accepted', 'revision_requested'])->default('pending');
            $table->text('revision_notes')->nullable();
            $table->timestamp('delivered_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['service_order_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_deliveries');
    }
};
