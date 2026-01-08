<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminIpWhitelist
{
    /**
     * Handle an incoming request.
     *
     * Restricts admin panel access to whitelisted IP addresses.
     * If ADMIN_ALLOWED_IPS is empty, all IPs are allowed (disabled).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIpsConfig = config('security.admin.allowed_ips', '');

        // If no IPs configured, whitelist is disabled - allow all
        if (empty($allowedIpsConfig)) {
            return $next($request);
        }

        $allowedIps = array_filter(
            array_map('trim', explode(',', $allowedIpsConfig))
        );

        // If after filtering we have no valid IPs, allow all
        if (empty($allowedIps)) {
            return $next($request);
        }

        $clientIp = $request->ip();

        // Check if client IP is in the whitelist
        if ($this->isIpAllowed($clientIp, $allowedIps)) {
            return $next($request);
        }

        // Log the blocked access attempt
        Log::channel('fraud')->warning('Admin panel access blocked - IP not whitelisted', [
            'ip' => $clientIp,
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user()?->id,
        ]);

        abort(403, 'Access denied: Your IP address is not authorized to access the admin panel.');
    }

    /**
     * Check if the given IP is allowed.
     * Supports exact IP matching and CIDR notation.
     */
    protected function isIpAllowed(string $clientIp, array $allowedIps): bool
    {
        foreach ($allowedIps as $allowed) {
            // Check for CIDR notation
            if (str_contains($allowed, '/')) {
                if ($this->ipInCidr($clientIp, $allowed)) {
                    return true;
                }
            } elseif ($clientIp === $allowed) {
                // Exact IP match
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an IP address is within a CIDR range.
     */
    protected function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);

        // Handle IPv4
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ipLong = ip2long($ip);
            $subnetLong = ip2long($subnet);

            if ($ipLong === false || $subnetLong === false) {
                return false;
            }

            $mask = (int) $mask;
            $maskLong = -1 << (32 - $mask);

            return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
        }

        // Handle IPv6
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return $this->ipv6InCidr($ip, $subnet, (int) $mask);
        }

        return false;
    }

    /**
     * Check if an IPv6 address is within a CIDR range.
     */
    protected function ipv6InCidr(string $ip, string $subnet, int $mask): bool
    {
        $ipBin = inet_pton($ip);
        $subnetBin = inet_pton($subnet);

        if ($ipBin === false || $subnetBin === false) {
            return false;
        }

        // Calculate prefix
        $fullBytes = intdiv($mask, 8);
        $partialBits = $mask % 8;

        // Compare full bytes
        for ($i = 0; $i < $fullBytes; $i++) {
            if ($ipBin[$i] !== $subnetBin[$i]) {
                return false;
            }
        }

        // Compare partial byte if any
        if ($partialBits > 0 && $fullBytes < 16) {
            $partialMask = 0xFF << (8 - $partialBits);
            if ((ord($ipBin[$fullBytes]) & $partialMask) !== (ord($subnetBin[$fullBytes]) & $partialMask)) {
                return false;
            }
        }

        return true;
    }
}
