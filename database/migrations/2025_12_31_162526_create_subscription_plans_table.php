<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('billing_period', ['weekly', 'monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->integer('billing_interval')->default(1); // e.g., 1 month, 3 months
            $table->integer('trial_days')->default(0);
            $table->json('features')->nullable(); // List of features included
            $table->integer('max_downloads')->nullable(); // For products - downloads per period
            $table->integer('max_requests')->nullable(); // For services - requests per period
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('stripe_price_id')->nullable();
            $table->string('stripe_product_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['seller_id', 'is_active']);
            $table->index(['product_id', 'is_active']);
            $table->index(['service_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
