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
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escrow_transaction_id')->constrained()->cascadeOnDelete();
            $table->morphs('disputable'); // ServiceOrder, JobContract
            $table->foreignId('initiated_by')->constrained('users')->cascadeOnDelete();
            $table->enum('reason', [
                'not_delivered',
                'poor_quality',
                'not_as_described',
                'communication_issues',
                'deadline_missed',
                'other'
            ]);
            $table->text('description');
            $table->json('evidence')->nullable(); // File attachments
            $table->enum('status', [
                'open',
                'under_review',
                'resolved_buyer',
                'resolved_seller',
                'resolved_split',
                'closed'
            ])->default('open');
            $table->text('admin_notes')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->decimal('seller_amount', 10, 2)->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['escrow_transaction_id', 'status']);
            $table->index('initiated_by');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
