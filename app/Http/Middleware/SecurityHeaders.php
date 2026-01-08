<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Security headers to protect against common web vulnerabilities.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add headers if security is enabled
        if (!Setting::get('security_headers_enabled', true)) {
            return $response;
        }

        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // XSS Protection (legacy browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer Policy - control how much referrer info is sent
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // HSTS - Force HTTPS (only in production)
        if (config('app.env') === 'production' && $request->isSecure()) {
            $maxAge = Setting::get('hsts_max_age', 31536000); // 1 year default
            $response->headers->set(
                'Strict-Transport-Security',
                "max-age={$maxAge}; includeSubDomains; preload"
            );
        }

        // Permissions Policy - control browser features
        $response->headers->set('Permissions-Policy', $this->getPermissionsPolicy());

        // Content Security Policy
        $csp = $this->getContentSecurityPolicy($request);
        if ($csp) {
            // Use Report-Only mode if configured (for testing)
            $headerName = Setting::get('csp_report_only', false)
                ? 'Content-Security-Policy-Report-Only'
                : 'Content-Security-Policy';
            $response->headers->set($headerName, $csp);
        }

        // Cross-Origin policies (production only - can cause issues with local dev tools)
        if (config('app.env') === 'production') {
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
            $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        }

        return $response;
    }

    /**
     * Build Content Security Policy header.
     */
    protected function getContentSecurityPolicy(Request $request): ?string
    {
        // Skip CSP entirely for local development
        if (config('app.env') === 'local') {
            return null;
        }

        $appUrl = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';

        // Production CSP - balances security with functionality
        $directives = [
            "default-src" => ["'self'"],
            "script-src" => [
                "'self'",
                "'unsafe-inline'", // Required for Alpine.js and inline scripts
                "'unsafe-eval'", // Required for Alpine.js reactive expressions
                "https://cdn.jsdelivr.net",
                "https://cdnjs.cloudflare.com",
                "https://www.googletagmanager.com",
                "https://www.google-analytics.com",
                "https://connect.facebook.net",
                "https://js.stripe.com",
                "https://static.cloudflareinsights.com",
            ],
            "style-src" => [
                "'self'",
                "'unsafe-inline'", // Required for Tailwind and dynamic styles
                "https://fonts.googleapis.com",
                "https://fonts.bunny.net",
                "https://cdn.jsdelivr.net",
                "https://cdnjs.cloudflare.com",
            ],
            "font-src" => [
                "'self'",
                "https://fonts.gstatic.com",
                "https://fonts.bunny.net",
                "https://cdn.jsdelivr.net",
                "data:",
            ],
            "img-src" => [
                "'self'",
                "data:",
                "blob:",
                "https:",
                // Allow common CDNs and services
                "https://*.cloudinary.com",
                "https://*.amazonaws.com",
                "https://www.google-analytics.com",
                "https://www.facebook.com",
            ],
            "connect-src" => [
                "'self'",
                "https://api.stripe.com",
                "https://www.google-analytics.com",
                "https://region1.google-analytics.com",
                "wss://{$appUrl}",
            ],
            "frame-src" => [
                "'self'",
                "https://js.stripe.com",
                "https://hooks.stripe.com",
                "https://www.google.com",
                "https://www.youtube.com",
                "https://player.vimeo.com",
                "https://meet.jit.si", // Video calls
            ],
            "frame-ancestors" => ["'self'"],
            "form-action" => ["'self'", "https://checkout.stripe.com"],
            "base-uri" => ["'self'"],
            "object-src" => ["'none'"],
            "upgrade-insecure-requests" => [],
        ];

        // Add custom CSP sources from settings
        $customSources = Setting::get('csp_custom_sources', []);
        if (is_array($customSources)) {
            foreach ($customSources as $directive => $sources) {
                if (isset($directives[$directive]) && is_array($sources)) {
                    $directives[$directive] = array_merge($directives[$directive], $sources);
                }
            }
        }

        // Build CSP string
        $cspParts = [];
        foreach ($directives as $directive => $sources) {
            if (empty($sources)) {
                $cspParts[] = $directive;
            } else {
                $cspParts[] = $directive . ' ' . implode(' ', array_unique($sources));
            }
        }

        return implode('; ', $cspParts);
    }

    /**
     * Build Permissions Policy header.
     */
    protected function getPermissionsPolicy(): string
    {
        $policies = [
            'accelerometer' => '()',
            'autoplay' => '(self)',
            'camera' => '(self)', // Allow for video calls
            'display-capture' => '()',
            'encrypted-media' => '(self)',
            'fullscreen' => '(self)',
            'geolocation' => '()',
            'gyroscope' => '()',
            'magnetometer' => '()',
            'microphone' => '(self)', // Allow for video calls
            'midi' => '()',
            'payment' => '(self)',
            'picture-in-picture' => '(self)',
            'publickey-credentials-get' => '()',
            'screen-wake-lock' => '()',
            'usb' => '()',
            'web-share' => '(self)',
            'xr-spatial-tracking' => '()',
        ];

        $parts = [];
        foreach ($policies as $feature => $value) {
            $parts[] = "{$feature}={$value}";
        }

        return implode(', ', $parts);
    }
}
