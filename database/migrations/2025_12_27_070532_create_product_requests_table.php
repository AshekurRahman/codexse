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
        Schema::create('product_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Requester info (for guests)
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();

            // Product details
            $table->string('product_title');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description');
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->string('urgency')->default('normal'); // low, normal, high, urgent

            // Additional details
            $table->text('features')->nullable(); // Requested features
            $table->text('reference_urls')->nullable(); // Reference/example URLs
            $table->json('attachments')->nullable(); // JSON array of file paths

            // Status tracking
            $table->string('status')->default('pending'); // pending, reviewing, approved, fulfilled, rejected, closed
            $table->text('admin_notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('fulfilled_by_product_id')->nullable()->constrained('products')->nullOnDelete();

            // Timestamps
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('urgency');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_requests');
    }
};
