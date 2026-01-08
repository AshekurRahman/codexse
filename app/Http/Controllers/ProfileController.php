<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\GdprDataRequest;
use App\Models\User;
use App\Services\GdprService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Handle avatar upload if present
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        // Check if email is being changed
        $emailChanged = isset($validated['email']) && $validated['email'] !== $user->email;

        if ($emailChanged) {
            // Don't immediately update email - require verification
            $newEmail = $validated['email'];
            unset($validated['email']); // Remove from validated data so it's not filled

            // Update other fields
            $user->fill($validated);
            $user->save();

            // Initiate email change verification
            $user->initiateEmailChange($newEmail);

            return Redirect::route('profile.edit')->with('status', 'email-verification-sent');
        }

        // No email change, update normally
        $user->fill($validated);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Verify email change token.
     */
    public function verifyEmailChange(Request $request): RedirectResponse
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return Redirect::route('profile.edit')->with('error', 'Invalid verification link.');
        }

        // Find user with pending email change
        $user = User::where('pending_email', $email)
            ->where('email_change_token', $token)
            ->first();

        if (!$user) {
            return Redirect::route('profile.edit')->with('error', 'Invalid or expired verification link.');
        }

        // Confirm the email change
        if ($user->confirmEmailChange($token)) {
            // Log out and log back in if this is the current user
            if (auth()->check() && auth()->id() === $user->id) {
                return Redirect::route('profile.edit')->with('status', 'email-changed');
            }

            return Redirect::route('login')->with('status', 'Your email has been changed successfully. Please log in with your new email.');
        }

        return Redirect::route('profile.edit')->with('error', 'Failed to verify email. The link may have expired.');
    }

    /**
     * Cancel pending email change.
     */
    public function cancelEmailChange(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasPendingEmailChange()) {
            $user->cancelPendingEmailChange();
            return Redirect::route('profile.edit')->with('status', 'email-change-cancelled');
        }

        return Redirect::route('profile.edit');
    }

    /**
     * Resend email change verification.
     */
    public function resendEmailChangeVerification(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->hasPendingEmailChange()) {
            return Redirect::route('profile.edit')->with('error', 'No pending email change to verify.');
        }

        // Regenerate token and resend
        $user->initiateEmailChange($user->pending_email);

        return Redirect::route('profile.edit')->with('status', 'email-verification-sent');
    }

    /**
     * Delete the user's account using GDPR-compliant anonymization.
     * This replaces the previous hard delete with proper data anonymization.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        try {
            DB::beginTransaction();

            // Create a GDPR deletion request record for audit trail
            $gdprRequest = GdprDataRequest::create([
                'user_id' => $user->id,
                'type' => 'deletion',
                'status' => 'processing',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Use GdprService to properly anonymize user data
            $gdprService = app(GdprService::class);
            $gdprService->processDeletionRequest($gdprRequest);

            DB::commit();

            // Logout user after anonymization
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('User account anonymized via profile deletion', [
                'gdpr_request_id' => $gdprRequest->id,
            ]);

            return Redirect::to('/')->with('success', 'Your account has been deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user account: ' . $e->getMessage());

            return Redirect::back()->withErrors([
                'password' => 'An error occurred while deleting your account. Please try again or contact support.',
            ], 'userDeletion');
        }
    }
}
