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
        Schema::create('seller_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->onDelete('cascade');
            $table->enum('verification_type', ['identity', 'business', 'address'])->default('identity');
            $table->enum('document_type', ['passport', 'national_id', 'drivers_license', 'business_license', 'utility_bill', 'bank_statement'])->nullable();
            $table->string('document_number')->nullable();
            $table->string('document_front')->nullable(); // Path to front image
            $table->string('document_back')->nullable(); // Path to back image
            $table->string('selfie_with_document')->nullable(); // Path to selfie with document
            $table->string('full_name');
            $table->date('date_of_birth')->nullable();
            $table->string('country');
            $table->text('address')->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['seller_id', 'status']);
            $table->index(['verification_type', 'status']);
        });

        // Add verification fields to sellers table
        Schema::table('sellers', function (Blueprint $table) {
            $table->enum('verification_status', ['unverified', 'pending', 'verified', 'rejected'])->default('unverified')->after('is_verified');
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->json('verification_badges')->nullable()->after('verified_at'); // Store earned badges
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['verification_status', 'verified_at', 'verification_badges']);
        });

        Schema::dropIfExists('seller_verifications');
    }
};
