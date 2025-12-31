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
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_package_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title'); // Service name snapshot
            $table->text('description')->nullable(); // For custom orders
            $table->decimal('price', 10, 2);
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('seller_amount', 10, 2);
            $table->unsignedInteger('delivery_days');
            $table->unsignedInteger('revisions_allowed')->default(0);
            $table->unsignedInteger('revisions_used')->default(0);
            $table->enum('status', [
                'pending_payment',
                'pending_requirements',
                'ordered',
                'in_progress',
                'delivered',
                'revision_requested',
                'completed',
                'cancelled',
                'disputed'
            ])->default('pending_payment');
            $table->json('requirements_data')->nullable(); // Buyer's answers to requirements
            $table->text('delivery_notes')->nullable();
            $table->text('completion_notes')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('auto_complete_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index('service_id');
            $table->index('due_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
