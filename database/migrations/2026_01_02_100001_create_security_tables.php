<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Blocked IPs table
        if (!Schema::hasTable('blocked_ips')) {
            Schema::create('blocked_ips', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45)->index();
                $table->boolean('is_range')->default(false);
                $table->string('reason')->nullable();
                $table->string('blocked_by')->default('manual');
                $table->boolean('is_active')->default(true);
                $table->timestamp('expires_at')->nullable();
                $table->unsignedBigInteger('blocked_requests_count')->default(0);
                $table->timestamps();

                $table->index(['is_active', 'expires_at']);
            });
        }

        // Security logs table
        if (!Schema::hasTable('security_logs')) {
            Schema::create('security_logs', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45)->index();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('event_type')->index();
                $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->text('description');
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['event_type', 'created_at']);
                $table->index(['severity', 'created_at']);
            });
        }

        // Security alerts table
        if (!Schema::hasTable('security_alerts')) {
            Schema::create('security_alerts', function (Blueprint $table) {
                $table->id();
                $table->string('alert_type')->index();
                $table->enum('severity', ['low', 'medium', 'high', 'critical']);
                $table->string('title');
                $table->text('description');
                $table->json('metadata')->nullable();
                $table->boolean('is_resolved')->default(false);
                $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('resolved_at')->nullable();
                $table->text('resolution_notes')->nullable();
                $table->timestamps();

                $table->index(['is_resolved', 'severity']);
            });
        }

        // Failed login attempts (enhanced)
        if (!Schema::hasTable('failed_login_attempts')) {
            Schema::create('failed_login_attempts', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45)->index();
                $table->string('email')->nullable()->index();
                $table->string('username')->nullable();
                $table->string('user_agent')->nullable();
                $table->string('country_code', 2)->nullable();
                $table->boolean('is_blocked')->default(false);
                $table->timestamps();

                $table->index(['ip_address', 'created_at']);
                $table->index(['email', 'created_at']);
            });
        }

        // Password history for preventing reuse
        if (!Schema::hasTable('password_histories')) {
            Schema::create('password_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('password_hash');
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
            });
        }

        // Trusted devices
        if (!Schema::hasTable('trusted_devices')) {
            Schema::create('trusted_devices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('device_id', 64)->unique();
                $table->string('device_name')->nullable();
                $table->string('device_type')->nullable();
                $table->string('browser')->nullable();
                $table->string('platform')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['user_id', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('trusted_devices');
        Schema::dropIfExists('password_histories');
        Schema::dropIfExists('failed_login_attempts');
        Schema::dropIfExists('security_alerts');
        Schema::dropIfExists('security_logs');
        Schema::dropIfExists('blocked_ips');
    }
};
