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
        Schema::table('chatbot_faqs', function (Blueprint $table) {
            $table->unsignedInteger('hit_count')->default(0)->after('is_active');
            $table->boolean('is_suggested')->default(false)->after('hit_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chatbot_faqs', function (Blueprint $table) {
            $table->dropColumn(['hit_count', 'is_suggested']);
        });
    }
};
