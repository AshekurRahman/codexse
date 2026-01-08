<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BlockMaliciousIps
{
    /**
     * Block requests from known malicious IPs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Setting::get('ip_blocking_enabled', true)) {
            return $next($request);
        }

        $ip = $request->ip();

        // Check if IP is blocked (with caching for performance)
        if ($this->isBlocked($ip)) {
            $this->logBlockedAccess($request);

            return response()->view('errors.blocked', [
                'message' => 'Your IP address has been blocked due to suspicious activity.',
            ], 403);
        }

        // Check for suspicious patterns
        if ($this->detectSuspiciousRequest($request)) {
            $this->handleSuspiciousRequest($request);
        }

        return $next($request);
    }

    /**
     * Check if IP is blocked.
     */
    protected function isBlocked(string $ip): bool
    {
        return Cache::remember("blocked_ip:{$ip}", 300, function () use ($ip) {
            // Check database for blocked IP
            $blocked = BlockedIp::where('ip_address', $ip)
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->where('is_active', true)
                ->exists();

            if ($blocked) {
                return true;
            }

            // Check CIDR ranges
            $blockedRanges = BlockedIp::where('is_range', true)
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->pluck('ip_address');

            foreach ($blockedRanges as $range) {
                if ($this->ipInRange($ip, $range)) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Check if IP is in CIDR range.
     */
    protected function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        [$subnet, $bits] = explode('/', $range);
        $bits = (int) $bits;

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip = ip2long($ip);
            $subnet = ip2long($subnet);
            $mask = -1 << (32 - $bits);
            return ($ip & $mask) === ($subnet & $mask);
        }

        return false;
    }

    /**
     * Detect suspicious request patterns.
     */
    protected function detectSuspiciousRequest(Request $request): bool
    {
        $patterns = $this->getSuspiciousPatterns();
        $checkAreas = [
            $request->path(),
            $request->userAgent() ?? '',
            json_encode($request->query()),
        ];

        foreach ($checkAreas as $area) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $area)) {
                    return true;
                }
            }
        }

        // Check for common attack indicators
        $attackIndicators = [
            // Path traversal
            '../', '..\\', '%2e%2e',
            // Common exploit paths
            'wp-admin', 'wp-login', 'xmlrpc.php', 'wp-config',
            '.env', '.git', '.htaccess', 'composer.json',
            'phpmyadmin', 'pma', 'mysql', 'adminer',
            // Shell injection
            ';ls', ';cat', '|ls', '|cat', '`ls`', '$(ls)',
        ];

        $path = strtolower($request->path());
        $query = strtolower(json_encode($request->all()));

        foreach ($attackIndicators as $indicator) {
            if (str_contains($path, $indicator) || str_contains($query, $indicator)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get suspicious patterns to detect.
     */
    protected function getSuspiciousPatterns(): array
    {
        return [
            // SQL Injection patterns
            '/(\%27)|(\')|(\-\-)|(\%23)|(#)/i',
            '/((\%3D)|(=))[^\n]*((\%27)|(\')|(\-\-)|(\%3B)|(;))/i',
            '/\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/i',
            '/union.*select/i',
            '/select.*from/i',
            '/insert.*into/i',
            '/drop.*table/i',
            '/delete.*from/i',
            '/update.*set/i',

            // XSS patterns
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',

            // Command injection
            '/;.*\b(ls|cat|rm|mv|cp|chmod|chown|wget|curl|bash|sh|python|perl|php)\b/i',
            '/\|.*\b(ls|cat|rm|mv|cp|chmod|chown|wget|curl|bash|sh)\b/i',
            '/`[^`]*`/',

            // Path traversal
            '/\.\.\//',
            '/\.\.\\\\/',

            // Remote file inclusion
            '/(https?|ftp):\/\/.*\.(php|asp|jsp|cgi)/i',
        ];
    }

    /**
     * Handle suspicious request.
     */
    protected function handleSuspiciousRequest(Request $request): void
    {
        $ip = $request->ip();

        // Increment suspicious activity counter
        $key = "suspicious_count:{$ip}";
        $count = Cache::increment($key);

        if ($count === 1) {
            Cache::put($key, 1, now()->addHour());
        }

        // Log suspicious activity
        Log::channel('security')->warning('Suspicious request detected', [
            'ip' => $ip,
            'path' => $request->path(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
            'query' => $request->query(),
            'suspicious_count' => $count,
        ]);

        // Auto-block if threshold exceeded (database setting, then config, then default)
        $configThreshold = config('security.ip_blocking.threshold', 10);
        $threshold = Setting::get('auto_block_threshold', $configThreshold);
        if ($count >= $threshold) {
            $this->autoBlockIp($ip, 'Exceeded suspicious activity threshold');
        }
    }

    /**
     * Automatically block an IP address.
     */
    protected function autoBlockIp(string $ip, string $reason): void
    {
        // Database setting, then config, then default
        $configDuration = config('security.ip_blocking.duration_hours', 24);
        $blockDuration = Setting::get('auto_block_duration', $configDuration); // hours

        BlockedIp::updateOrCreate(
            ['ip_address' => $ip],
            [
                'reason' => $reason,
                'blocked_by' => 'system',
                'is_active' => true,
                'expires_at' => now()->addHours($blockDuration),
            ]
        );

        // Clear cache
        Cache::forget("blocked_ip:{$ip}");
        Cache::forget("suspicious_count:{$ip}");

        Log::channel('security')->alert('IP automatically blocked', [
            'ip' => $ip,
            'reason' => $reason,
            'duration_hours' => $blockDuration,
        ]);
    }

    /**
     * Log blocked access attempt.
     */
    protected function logBlockedAccess(Request $request): void
    {
        Log::channel('security')->info('Blocked IP access attempt', [
            'ip' => $request->ip(),
            'path' => $request->path(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
