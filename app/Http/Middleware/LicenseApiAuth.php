<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LicenseApiAuth
{
    /**
     * Handle an incoming request.
     *
     * Validates API key for license endpoints to prevent abuse.
     * The API key should be passed in the X-API-Key header.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get configured API key
        $configuredKey = Setting::get('license_api_key');

        // If no API key is configured, reject all requests for security
        if (empty($configuredKey)) {
            Log::critical('License API: No API key configured - rejecting request', [
                'ip' => $request->ip(),
                'endpoint' => $request->path(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'API not configured. Please contact administrator.',
            ], 503);
        }

        // Get API key from request
        $providedKey = $request->header('X-API-Key') ?? $request->query('api_key');

        if (empty($providedKey)) {
            Log::warning('License API: Missing API key', [
                'ip' => $request->ip(),
                'endpoint' => $request->path(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'API key required',
            ], 401);
        }

        // Validate API key using timing-safe comparison
        if (!hash_equals($configuredKey, $providedKey)) {
            Log::warning('License API: Invalid API key', [
                'ip' => $request->ip(),
                'endpoint' => $request->path(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Invalid API key',
            ], 401);
        }

        return $next($request);
    }
}
