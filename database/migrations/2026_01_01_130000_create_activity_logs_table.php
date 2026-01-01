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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('causer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('causer_type')->nullable(); // user, admin, system
            $table->string('action'); // login, logout, create, update, delete, etc.
            $table->string('category'); // auth, profile, order, security, etc.
            $table->string('description');
            $table->string('subject_type')->nullable(); // Model class name
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('properties')->nullable(); // Additional data
            $table->json('old_values')->nullable(); // For tracking changes
            $table->json('new_values')->nullable(); // For tracking changes
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('browser')->nullable();
            $table->string('platform')->nullable(); // OS
            $table->string('country_code', 2)->nullable();
            $table->string('city')->nullable();
            $table->string('risk_level')->nullable(); // low, medium, high
            $table->boolean('is_suspicious')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'action']);
            $table->index(['user_id', 'category']);
            $table->index(['subject_type', 'subject_id']);
            $table->index('action');
            $table->index('category');
            $table->index('created_at');
            $table->index('is_suspicious');
        });

        // Create login attempts table for security tracking
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->boolean('successful')->default(false);
            $table->string('failure_reason')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('city')->nullable();
            $table->timestamps();

            $table->index(['email', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('successful');
        });

        // Create user sessions table
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('logged_out_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_current']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('activity_logs');
    }
};
