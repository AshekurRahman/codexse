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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('orders', 'fraud_score')) {
                $table->decimal('fraud_score', 5, 2)->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('orders', 'user_agent')) {
                $table->string('user_agent')->nullable()->after('fraud_score');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'fraud_score', 'user_agent']);
        });
    }
};
