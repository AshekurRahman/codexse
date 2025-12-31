<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referred_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['signup', 'purchase', 'bonus'])->default('signup');
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->enum('status', ['pending', 'credited', 'withdrawn', 'expired'])->default('pending');
            $table->timestamp('credited_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // Create referral settings table
        Schema::create('referral_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('referral_settings')->insert([
            ['key' => 'signup_reward_referrer', 'value' => '5.00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'signup_reward_referred', 'value' => '5.00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'purchase_commission_percent', 'value' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'min_withdrawal_amount', 'value' => '20.00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'referral_program_enabled', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_settings');
        Schema::dropIfExists('referral_rewards');
    }
};
