<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));

            // Send welcome email after verification
            try {
                $request->user()->notify(new WelcomeNotification());
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email: ' . $e->getMessage());
            }
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
