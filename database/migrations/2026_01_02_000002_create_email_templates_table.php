<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing columns to existing email_templates table
        if (Schema::hasTable('email_templates')) {
            Schema::table('email_templates', function (Blueprint $table) {
                if (!Schema::hasColumn('email_templates', 'subject')) {
                    $table->string('subject')->nullable()->after('name');
                }
                if (!Schema::hasColumn('email_templates', 'variables')) {
                    $table->json('variables')->nullable()->after('category');
                }
                if (!Schema::hasColumn('email_templates', 'is_system')) {
                    $table->boolean('is_system')->default(false)->after('is_active');
                }
            });
        } else {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->string('subject')->nullable();
                $table->text('html_content')->nullable();
                $table->string('category')->default('general');
                $table->json('variables')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_system')->default(false);
                $table->timestamps();

                $table->index('category');
                $table->index('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('email_templates')) {
            Schema::table('email_templates', function (Blueprint $table) {
                $table->dropColumn(['subject', 'variables', 'is_system']);
            });
        }
    }
};
