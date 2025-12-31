<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If there's a pending 2FA verification
        if (session('2fa:user_id') && !$request->routeIs('two-factor.challenge', 'two-factor.verify', 'logout')) {
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
