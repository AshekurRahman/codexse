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
        Schema::create('job_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_contract_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('seller_amount', 10, 2);
            $table->date('due_date')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->enum('status', [
                'pending',
                'funded',
                'in_progress',
                'submitted',
                'revision_requested',
                'approved',
                'released',
                'cancelled'
            ])->default('pending');
            $table->text('submission_notes')->nullable();
            $table->json('submission_files')->nullable();
            $table->text('revision_notes')->nullable();
            $table->timestamp('funded_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->index(['job_contract_id', 'status']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_milestones');
    }
};
