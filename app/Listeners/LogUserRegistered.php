<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Registered;

class LogUserRegistered
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        if ($event->user) {
            ActivityLogService::logAccountCreated($event->user);
        }
    }
}
