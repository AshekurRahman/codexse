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
        Schema::create('job_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->text('cover_letter');
            $table->decimal('proposed_price', 10, 2);
            $table->unsignedInteger('proposed_duration'); // in days
            $table->json('milestones')->nullable(); // For milestone-based proposals
            $table->json('attachments')->nullable();
            $table->enum('status', ['pending', 'shortlisted', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['job_posting_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->unique(['job_posting_id', 'seller_id']); // One proposal per seller per job
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_proposals');
    }
};
