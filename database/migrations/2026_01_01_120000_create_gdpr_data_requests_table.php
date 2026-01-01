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
        // Create data requests table
        Schema::create('gdpr_data_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['export', 'deletion', 'rectification', 'restriction'])->default('export');
            $table->enum('status', ['pending', 'processing', 'completed', 'rejected', 'cancelled'])->default('pending');
            $table->text('reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('export_file_path')->nullable();
            $table->timestamp('export_expires_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('data_categories')->nullable();
            $table->json('verification_data')->nullable();
            $table->boolean('identity_verified')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'type', 'status']);
            $table->index('status');
        });

        // Create consent logs table
        Schema::create('gdpr_consent_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('consent_type'); // marketing, analytics, necessary, third_party
            $table->boolean('granted')->default(false);
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('consent_text')->nullable();
            $table->string('version')->nullable();
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'consent_type']);
        });

        // Create data processing activities log
        Schema::create('gdpr_processing_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('activity_type'); // data_access, data_update, data_deletion, data_export
            $table->string('data_category'); // personal, financial, usage, preferences
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'activity_type']);
            $table->index('created_at');
        });

        // Add consent fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('marketing_consent')->default(false);
            $table->boolean('analytics_consent')->default(true);
            $table->boolean('third_party_consent')->default(false);
            $table->timestamp('privacy_policy_accepted_at')->nullable();
            $table->string('privacy_policy_version')->nullable();
            $table->timestamp('gdpr_deletion_requested_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'marketing_consent',
                'analytics_consent',
                'third_party_consent',
                'privacy_policy_accepted_at',
                'privacy_policy_version',
                'gdpr_deletion_requested_at',
            ]);
        });

        Schema::dropIfExists('gdpr_processing_logs');
        Schema::dropIfExists('gdpr_consent_logs');
        Schema::dropIfExists('gdpr_data_requests');
    }
};
