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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // USD, EUR, GBP, etc.
            $table->string('name'); // US Dollar, Euro, etc.
            $table->string('symbol', 10); // $, €, £, etc.
            $table->string('symbol_position', 10)->default('before'); // before or after
            $table->decimal('exchange_rate', 12, 6)->default(1.000000); // Rate relative to base currency
            $table->string('decimal_separator', 1)->default('.');
            $table->string('thousand_separator', 1)->default(',');
            $table->unsignedTinyInteger('decimal_places')->default(2);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('rate_updated_at')->nullable();
            $table->timestamps();
        });

        // Add currency preference to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('USD')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('currency_code');
        });

        Schema::dropIfExists('currencies');
    }
};
