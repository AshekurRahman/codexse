<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_calls', function (Blueprint $table) {
            $table->id();
            $table->string('room_id')->unique();
            $table->foreignId('host_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('participant_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('conversation_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('service_order_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, active, ended, missed, cancelled
            $table->string('type')->default('video'); // video, audio
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->string('provider')->default('agora'); // agora, twilio, daily
            $table->json('provider_data')->nullable(); // tokens, channel info, etc.
            $table->text('notes')->nullable();
            $table->string('recording_url')->nullable();
            $table->timestamps();

            $table->index(['host_id', 'status']);
            $table->index(['participant_id', 'status']);
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_calls');
    }
};
