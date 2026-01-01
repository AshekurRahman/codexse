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
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "California Sales Tax"
            $table->string('country_code', 2)->default('US'); // ISO 3166-1 alpha-2
            $table->string('state_code', 10); // US state code e.g., CA, NY, TX
            $table->decimal('rate', 5, 2); // Tax rate percentage e.g., 7.25
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['country_code', 'state_code']);
            $table->index(['country_code', 'state_code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
