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
        Schema::table('sellers', function (Blueprint $table) {
            $table->boolean('is_on_vacation')->default(false)->after('is_verified');
            $table->text('vacation_message')->nullable()->after('is_on_vacation');
            $table->timestamp('vacation_started_at')->nullable()->after('vacation_message');
            $table->timestamp('vacation_ends_at')->nullable()->after('vacation_started_at');
            $table->boolean('vacation_auto_reply')->default(true)->after('vacation_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn([
                'is_on_vacation',
                'vacation_message',
                'vacation_started_at',
                'vacation_ends_at',
                'vacation_auto_reply',
            ]);
        });
    }
};
