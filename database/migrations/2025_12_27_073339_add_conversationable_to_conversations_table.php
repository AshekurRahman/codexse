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
        Schema::table('conversations', function (Blueprint $table) {
            $table->nullableMorphs('conversationable');
            $table->enum('type', ['general', 'service_order', 'job_posting', 'job_contract', 'custom_quote'])->default('general')->after('subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropMorphs('conversationable');
            $table->dropColumn('type');
        });
    }
};
