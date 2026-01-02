<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('phone_number');
            $table->string('type'); // order_confirmation, order_shipped, order_delivered, verification, etc.
            $table->text('message');
            $table->string('status')->default('pending'); // pending, sent, delivered, failed
            $table->string('provider')->nullable(); // twilio, nexmo, sns
            $table->string('provider_message_id')->nullable();
            $table->json('provider_response')->nullable();
            $table->text('error_message')->nullable();
            $table->decimal('cost', 8, 4)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
