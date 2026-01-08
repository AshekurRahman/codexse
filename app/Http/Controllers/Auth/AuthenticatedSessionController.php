<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse|JsonResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Check if 2FA is enabled
        if ($user->hasTwoFactorEnabled()) {
            Auth::logout();
            $request->session()->put('2fa:user_id', $user->id);
            $request->session()->put('2fa:remember', $request->boolean('remember'));

            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();

        // Check if email verification is required but not completed
        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            $redirectUrl = route('verification.notice');
        } else {
            $redirectUrl = redirect()->intended(route('dashboard', absolute: false))->getTargetUrl();
        }

        // Security: Validate redirect URL to prevent open redirects
        $redirectUrl = $this->validateRedirectUrl($redirectUrl);

        if ($request->expectsJson()) {
            return response()->json(['redirect' => $redirectUrl]);
        }

        return redirect($redirectUrl);
    }

    /**
     * Validate redirect URL to prevent open redirect attacks.
     */
    protected function validateRedirectUrl(string $url): string
    {
        $parsed = parse_url($url);

        // Allow relative URLs
        if (!isset($parsed['host'])) {
            return $url;
        }

        // Get allowed hosts (current app domain)
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        // Only allow redirects to the same host
        if ($parsed['host'] === $appHost) {
            return $url;
        }

        // Default to dashboard for external/untrusted URLs
        return route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
