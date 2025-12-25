<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referred_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->decimal('order_amount', 10, 2)->nullable();
            $table->decimal('commission_amount', 10, 2)->nullable();
            $table->enum('status', ['clicked', 'registered', 'purchased', 'paid'])->default('clicked');
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->index('affiliate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
