<?php

namespace App\Listeners;

use App\Models\LoginAttempt;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        $email = $event->credentials['email'] ?? '';
        $user = $event->user;

        ActivityLogService::logLoginFailed(
            $email,
            LoginAttempt::REASON_INVALID_CREDENTIALS,
            $user
        );
    }
}
