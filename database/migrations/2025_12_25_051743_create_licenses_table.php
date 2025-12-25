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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('license_key', 19)->unique(); // XXXX-XXXX-XXXX-XXXX
            $table->string('license_type')->default('regular'); // regular, extended
            $table->enum('status', ['active', 'suspended', 'expired', 'revoked'])->default('active');
            $table->unsignedInteger('activations_count')->default(0);
            $table->unsignedInteger('max_activations')->default(1);
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['license_key', 'status']);
            $table->index('user_id');
            $table->index('product_id');
        });

        // Create license_activations table for tracking each activation
        Schema::create('license_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->cascadeOnDelete();
            $table->string('domain')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('machine_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_activations');
        Schema::dropIfExists('licenses');
    }
};
