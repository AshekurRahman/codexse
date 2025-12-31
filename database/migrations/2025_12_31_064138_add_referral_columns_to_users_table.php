<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 10)->unique()->nullable()->after('email');
            $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
            $table->decimal('referral_balance', 10, 2)->default(0)->after('referred_by');
            $table->integer('total_referrals')->default(0)->after('referral_balance');
            $table->integer('successful_referrals')->default(0)->after('total_referrals');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn([
                'referral_code',
                'referred_by',
                'referral_balance',
                'total_referrals',
                'successful_referrals',
            ]);
        });
    }
};
