<?php

namespace App\Http\Requests\Auth;

use App\Models\LoginAttempt;
use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Maximum failed login attempts before account lockout.
     */
    protected const MAX_ATTEMPTS = 5;

    /**
     * Account lockout duration in minutes.
     */
    protected const LOCKOUT_MINUTES = 15;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        $this->ensureAccountIsNotLocked();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            $this->handleFailedLogin();

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Successful login - clear rate limiter and reset failed attempts
        RateLimiter::clear($this->throttleKey());
        $this->resetFailedAttempts();

        // Log successful login
        LoginAttempt::recordAttempt(
            $this->input('email'),
            true,
            Auth::id()
        );
    }

    /**
     * Handle failed login attempt.
     */
    protected function handleFailedLogin(): void
    {
        RateLimiter::hit($this->throttleKey());

        // Find user by email to track attempts
        $user = User::where('email', $this->input('email'))->first();

        // Log the failed attempt
        LoginAttempt::recordAttempt(
            $this->input('email'),
            false,
            $user?->id,
            LoginAttempt::REASON_INVALID_CREDENTIALS
        );

        if ($user) {
            $user->increment('failed_login_attempts');

            // Check if account should be locked
            if ($user->failed_login_attempts >= self::MAX_ATTEMPTS) {
                $user->update([
                    'locked_until' => now()->addMinutes(self::LOCKOUT_MINUTES),
                ]);

                // Log security event
                SecurityLog::log(
                    'account_locked',
                    "Account locked after {$user->failed_login_attempts} failed login attempts",
                    'high',
                    $this->ip(),
                    $user->id,
                    [
                        'email' => $user->email,
                        'failed_attempts' => $user->failed_login_attempts,
                        'locked_until' => $user->locked_until,
                        'user_agent' => $this->userAgent(),
                    ]
                );
            }
        }

        // Log to security log for monitoring
        SecurityLog::log(
            'login_failed',
            "Failed login attempt for: {$this->input('email')}",
            'medium',
            $this->ip(),
            $user?->id,
            [
                'email' => $this->input('email'),
                'user_agent' => $this->userAgent(),
            ]
        );
    }

    /**
     * Ensure the account is not locked.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function ensureAccountIsNotLocked(): void
    {
        $user = User::where('email', $this->input('email'))->first();

        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            $seconds = now()->diffInSeconds($user->locked_until);

            SecurityLog::log(
                'login_failed',
                "Attempted login to locked account: {$user->email}",
                'medium',
                $this->ip(),
                $user->id,
                [
                    'locked_until' => $user->locked_until,
                    'user_agent' => $this->userAgent(),
                ]
            );

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        // Automatically unlock if lockout period has passed
        if ($user && $user->locked_until && $user->locked_until->isPast()) {
            $user->update([
                'locked_until' => null,
                'failed_login_attempts' => 0,
            ]);
        }
    }

    /**
     * Reset failed login attempts on successful login.
     */
    protected function resetFailedAttempts(): void
    {
        $user = Auth::user();

        if ($user) {
            $user->update([
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        SecurityLog::log(
            'rate_limited',
            "Login rate limited for IP: {$this->ip()}",
            'medium',
            $this->ip(),
            null,
            [
                'email' => $this->input('email'),
                'user_agent' => $this->userAgent(),
            ]
        );

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
