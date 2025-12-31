<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create email templates table
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->text('description')->nullable();
            $table->longText('html_content');
            $table->string('category')->default('general');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add sending controls to campaigns
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->foreignId('email_template_id')->nullable()->after('content')->constrained()->nullOnDelete();
            $table->integer('daily_limit')->default(100)->after('email_template_id');
            $table->integer('sending_duration_days')->default(1)->after('daily_limit');
            $table->integer('daily_increment')->default(0)->after('sending_duration_days');
            $table->integer('current_day')->default(0)->after('daily_increment');
            $table->integer('today_sent_count')->default(0)->after('current_day');
            $table->date('last_send_date')->nullable()->after('today_sent_count');
            $table->date('campaign_start_date')->nullable()->after('last_send_date');
            $table->date('campaign_end_date')->nullable()->after('campaign_start_date');
            $table->enum('sending_status', ['idle', 'running', 'paused', 'completed', 'stopped'])->default('idle')->after('status');
            $table->timestamp('paused_at')->nullable()->after('sending_status');
            $table->timestamp('stopped_at')->nullable()->after('paused_at');
            $table->text('sending_log')->nullable()->after('stopped_at');
        });
    }

    public function down(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropForeign(['email_template_id']);
            $table->dropColumn([
                'email_template_id',
                'daily_limit',
                'sending_duration_days',
                'daily_increment',
                'current_day',
                'today_sent_count',
                'last_send_date',
                'campaign_start_date',
                'campaign_end_date',
                'sending_status',
                'paused_at',
                'stopped_at',
                'sending_log',
            ]);
        });

        Schema::dropIfExists('email_templates');
    }
};
