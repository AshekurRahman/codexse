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
        Schema::table('job_proposals', function (Blueprint $table) {
            $table->string('duration_type', 20)->default('days')->after('proposed_duration');
            $table->timestamp('withdrawn_at')->nullable()->after('reviewed_at');
            $table->timestamp('rejected_at')->nullable()->after('withdrawn_at');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_proposals', function (Blueprint $table) {
            $table->dropColumn(['duration_type', 'withdrawn_at', 'rejected_at', 'rejection_reason']);
        });
    }
};
