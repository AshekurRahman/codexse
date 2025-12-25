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
        Schema::create('social_login_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // google, facebook, github, twitter
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique('provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_login_settings');
    }
};
