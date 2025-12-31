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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Basic", "Pro", "Enterprise"
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('regular_price', 10, 2)->nullable(); // For showing discounts
            $table->json('features')->nullable(); // List of included features
            $table->integer('downloads_limit')->default(0); // 0 = unlimited
            $table->integer('support_months')->default(6); // Support duration
            $table->integer('updates_months')->default(12); // Updates duration
            $table->string('license_type')->default('regular'); // regular, extended
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'slug']);
        });

        // Add has_variations flag to products table
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_variations')->default(false)->after('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('has_variations');
        });

        Schema::dropIfExists('product_variations');
    }
};
