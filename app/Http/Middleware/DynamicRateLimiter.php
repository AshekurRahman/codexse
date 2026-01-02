<?php

namespace App\Http\Middleware;

use App\Filament\Admin\Pages\RateLimitingSettings;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DynamicRateLimiter
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $type = 'global'): Response
    {
        $settings = RateLimitingSettings::getRateLimitSettings();

        // Check if rate limiting is enabled
        if (!($settings['enabled'] ?? true)) {
            return $next($request);
        }

        // Check if IP is whitelisted
        $ip = $request->ip();
        if (RateLimitingSettings::isWhitelisted($ip)) {
            return $next($request);
        }

        // Get rate limit settings for this type
        $config = $settings[$type] ?? $settings['global'];
        $maxAttempts = $config['limit'] ?? 60;
        $decayMinutes = $config['decay'] ?? 1;

        // Create unique key for this request
        $key = $this->resolveRequestSignature($request, $type);

        // Check if rate limit exceeded
        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $this->incrementBlockedCount();

            return response()->json([
                'message' => $settings['message'] ?? 'Too many requests. Please try again later.',
                'retry_after' => $this->limiter->availableIn($key),
            ], 429)->withHeaders([
                'Retry-After' => $this->limiter->availableIn($key),
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        // Increment attempts
        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $this->limiter->remaining($key, $maxAttempts),
        ]);
    }

    /**
     * Resolve the request signature for rate limiting.
     */
    protected function resolveRequestSignature(Request $request, string $type): string
    {
        $user = $request->user();

        if ($user) {
            return 'rate_limit:' . $type . ':user:' . $user->id;
        }

        return 'rate_limit:' . $type . ':ip:' . $request->ip();
    }

    /**
     * Increment the blocked request counter.
     */
    protected function incrementBlockedCount(): void
    {
        $totalKey = 'rate_limit_blocked_count';
        $todayKey = 'rate_limit_blocked_today';

        cache()->increment($totalKey);
        cache()->increment($todayKey);

        // Set expiry for today's count at midnight
        if (!cache()->has($todayKey . '_expiry')) {
            cache()->put($todayKey, 1, now()->endOfDay());
            cache()->put($todayKey . '_expiry', true, now()->endOfDay());
        }
    }
}
