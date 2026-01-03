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
