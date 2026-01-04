<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordHistory;
use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                // Check if new password was previously used
                if (PasswordHistory::wasUsedBefore($user->id, $request->password)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'password' => 'You cannot reuse a recent password. Please choose a different password.',
                    ]);
                }

                // Save current password to history before updating (only if user has a password)
                if ($user->password !== null) {
                    PasswordHistory::store($user->id, $user->password);
                }

                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                    'locked_until' => null,
                    'failed_login_attempts' => 0,
                ])->save();

                // Invalidate all sessions for this user
                DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->delete();

                // Log the password reset
                SecurityLog::log(
                    'password_reset',
                    'Password reset via email link',
                    'medium',
                    $request->ip(),
                    $user->id,
                    ['user_agent' => $request->userAgent()]
                );

                event(new PasswordReset($user));
            }
        );

        $success = $status === Password::PASSWORD_RESET;

        if ($request->expectsJson()) {
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => __($status),
                    'redirect' => route('login'),
                ]);
            }

            return response()->json([
                'success' => false,
                'errors' => ['email' => [__($status)]],
            ], 422);
        }

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $success
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
