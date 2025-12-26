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
        Schema::create('ai_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('status')->default('active');
            $table->string('subject')->nullable();
            $table->json('context')->nullable();
            $table->unsignedInteger('message_count')->default(0);
            $table->unsignedInteger('total_tokens_used')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['session_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_sessions');
    }
};
