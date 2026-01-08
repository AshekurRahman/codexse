<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     * Works without requiring authentication.
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Invalid verification link.');
        }

        // Verify the hash matches the user's email
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')
                ->with('error', 'Invalid verification link.');
        }

        // Check if URL signature is valid
        if (!$request->hasValidSignature()) {
            return redirect()->route('login')
                ->with('error', 'Verification link has expired. Please request a new one.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            // If user is logged in, go to dashboard
            if (Auth::check()) {
                return redirect()->route('dashboard')
                    ->with('status', 'Your email is already verified.');
            }
            // Otherwise go to login with message
            return redirect()->route('login')
                ->with('status', 'Your email is already verified. Please log in.');
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            // Send welcome email after verification
            try {
                $user->notify(new WelcomeNotification());
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email: ' . $e->getMessage());
            }
        }

        // Log the user in automatically after verification
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('status', 'Your email has been verified successfully!');
    }
}
