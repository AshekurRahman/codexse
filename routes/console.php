<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Process queue jobs every minute (for emails, notifications, etc.)
Schedule::command('queue:work --stop-when-empty --max-time=55')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Process email campaigns every hour during business hours
Schedule::command('campaigns:process')
    ->hourly()
    ->between('6:00', '22:00')
    ->withoutOverlapping()
    ->runInBackground();

// Process escrow auto-releases daily
Schedule::command('escrow:auto-release')
    ->daily()
    ->at('00:00')
    ->withoutOverlapping();

// Expire old wallet holds every minute
Schedule::command('wallet:expire-holds')
    ->everyMinute()
    ->withoutOverlapping();

// Clean up expired wallet idempotency keys daily
Schedule::command('wallet:cleanup-idempotency')
    ->daily()
    ->at('01:00')
    ->withoutOverlapping();

// Automated database backups (encrypted via Spatie Laravel Backup)
Schedule::command('backup:run --only-db')
    ->daily()
    ->at('02:00')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Scheduled backup completed successfully');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Scheduled backup failed');
    });

// Clean up old backups according to retention policy
Schedule::command('backup:clean')
    ->daily()
    ->at('03:00')
    ->withoutOverlapping();

// Monitor backup health
Schedule::command('backup:monitor')
    ->daily()
    ->at('04:00');

// Clean up old processed webhooks
Schedule::command('webhooks:cleanup --days=30')
    ->daily()
    ->at('05:00')
    ->withoutOverlapping();

// Clean up orphaned temp uploads (files older than 24 hours)
Schedule::command('uploads:cleanup-temp --hours=24')
    ->daily()
    ->at('06:00')
    ->withoutOverlapping();
