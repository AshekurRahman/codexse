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
        Schema::create('fraud_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('alert_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Transaction reference (polymorphic)
            $table->nullableMorphs('alertable'); // order_id, wallet_transaction_id, escrow_transaction_id

            // Alert details
            $table->string('type'); // velocity, high_amount, new_account, failed_attempts, geo_anomaly, device_anomaly, card_testing, etc.
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'reviewing', 'confirmed_fraud', 'false_positive', 'resolved'])->default('pending');
            $table->decimal('risk_score', 5, 2)->default(0); // 0-100 score

            // Transaction details
            $table->decimal('transaction_amount', 12, 2)->nullable();
            $table->string('transaction_currency', 3)->default('USD');
            $table->string('payment_method')->nullable();
            $table->string('payment_id')->nullable(); // stripe_payment_intent, paypal_order_id, etc.

            // Detection details
            $table->json('detection_rules'); // Which rules triggered
            $table->json('detection_data')->nullable(); // Raw data that triggered alert
            $table->text('description')->nullable();

            // Request/Session info
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('city')->nullable();

            // Resolution
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->enum('action_taken', ['none', 'blocked', 'refunded', 'account_suspended', 'account_banned'])->nullable();

            // Auto-actions taken
            $table->boolean('auto_blocked')->default(false);
            $table->boolean('notification_sent')->default(false);

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['type', 'severity']);
            $table->index(['status', 'created_at']);
            $table->index('ip_address');
            $table->index('risk_score');
        });

        // Create fraud rules configuration table
        Schema::create('fraud_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // velocity_check, high_amount, etc.
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->integer('risk_score')->default(25); // Base score when triggered
            $table->json('config')->nullable(); // Rule-specific configuration
            $table->boolean('auto_block')->default(false); // Automatically block transaction
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Create IP blocklist table
        Schema::create('fraud_ip_blocklist', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('reason')->nullable();
            $table->enum('type', ['manual', 'auto'])->default('manual');
            $table->foreignId('blocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique('ip_address');
            $table->index('expires_at');
        });

        // Create user fraud flags table
        Schema::create('fraud_user_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('flag_type', ['warning', 'restricted', 'suspended', 'banned'])->default('warning');
            $table->string('reason')->nullable();
            $table->foreignId('flagged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index('flag_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fraud_user_flags');
        Schema::dropIfExists('fraud_ip_blocklist');
        Schema::dropIfExists('fraud_rules');
        Schema::dropIfExists('fraud_alerts');
    }
};
