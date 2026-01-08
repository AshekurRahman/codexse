<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminTwoFactorMiddleware
{
    /**
     * Enforce 2FA for admin users.
     *
     * Admin accounts must have 2FA enabled for security.
     * If not enabled, redirect to 2FA setup page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Only enforce for authenticated admin users
        if (!$user || !$user->is_admin) {
            return $next($request);
        }

        // Skip if already on 2FA setup/management routes
        if ($request->routeIs('two-factor.*', 'logout', 'filament.admin.auth.logout')) {
            return $next($request);
        }

        // Check if 2FA is enabled
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.setup')
                ->with('warning', 'Admin accounts require two-factor authentication. Please set up 2FA to continue.');
        }

        return $next($request);
    }
}
