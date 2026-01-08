<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class LogUserRegistered
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        if ($event->user) {
            try {
                ActivityLogService::logAccountCreated($event->user);
            } catch (\Exception $e) {
                // Don't let logging failures prevent other listeners (like email verification)
                Log::warning('Failed to log user registration', [
                    'user_id' => $event->user->id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
