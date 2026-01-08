<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordHistory;
use App\Models\SecurityLog;
use App\Services\SecurityNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $hashedPassword = Hash::make($validated['password']);

        // Check if new password was previously used
        if (PasswordHistory::wasUsedBefore($user->id, $validated['password'])) {
            return back()->withErrors([
                'password' => 'You cannot reuse a recent password. Please choose a different password.',
            ], 'updatePassword');
        }

        // Save current password to history before updating
        PasswordHistory::store($user->id, $user->password);

        // Update password and clear any lockout
        $user->update([
            'password' => $hashedPassword,
            'locked_until' => null,
            'failed_login_attempts' => 0,
        ]);

        // Invalidate all other sessions for security
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        // Log the password change
        SecurityLog::log(
            'password_reset',
            'Password changed by user',
            'medium',
            $request->ip(),
            $user->id,
            ['user_agent' => $request->userAgent()]
        );

        // Send notification to user about password change
        app(SecurityNotificationService::class)->notifyPasswordChanged(
            user: $user,
            ipAddress: $request->ip(),
            userAgent: $request->userAgent()
        );

        return back()->with('status', 'password-updated');
    }
}
