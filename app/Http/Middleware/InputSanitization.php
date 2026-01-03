<?php

namespace App\Http\Middleware;

use App\Models\SecurityLog;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class InputSanitization
{
    /**
     * SQL injection patterns.
     */
    protected array $sqlPatterns = [
        '/\b(union\s+(all\s+)?select)\b/i',
        '/\b(select\s+.+\s+from)\b/i',
        '/\b(insert\s+into)\b/i',
        '/\b(delete\s+from)\b/i',
        '/\b(drop\s+(table|database))\b/i',
        '/\b(update\s+.+\s+set)\b/i',
        '/\b(alter\s+table)\b/i',
        '/\b(truncate\s+table)\b/i',
        '/\b(exec(ute)?)\s*\(/i',
        '/\b(xp_)\w+/i',
        '/\b(sp_)\w+/i',
        '/\b(having)\s+[\d\w]+\s*[=<>]/i',
        '/\b(group\s+by).+\b(having)\b/i',
        '/;\s*(select|insert|update|delete|drop|alter|create)/i',
        '/--\s*$/m',
        '/\/\*.*?\*\//s',
        '/\b(benchmark|sleep|waitfor)\s*\(/i',
        '/\b(load_file|into\s+outfile)\b/i',
        '/\b(information_schema)\b/i',
        '/\b(0x[0-9a-f]+)\b/i', // Hex encoded strings
        '/char\s*\(\s*\d+\s*\)/i', // CHAR() function
    ];

    /**
     * XSS patterns.
     */
    protected array $xssPatterns = [
        '/<script\b[^>]*>.*?<\/script>/is',
        '/javascript\s*:/i',
        '/vbscript\s*:/i',
        '/on\w+\s*=\s*["\'][^"\']*["\']/i', // Event handlers
        '/on\w+\s*=/i',
        '/<\s*iframe/i',
        '/<\s*object/i',
        '/<\s*embed/i',
        '/<\s*applet/i',
        '/<\s*meta/i',
        '/<\s*link/i',
        '/<\s*style/i',
        '/<\s*form/i',
        '/expression\s*\(/i', // CSS expression
        '/url\s*\(\s*["\']?javascript/i',
        '/\beval\s*\(/i',
        '/\bdocument\s*\./i',
        '/\bwindow\s*\./i',
        '/\balert\s*\(/i',
        '/\bconfirm\s*\(/i',
        '/\bprompt\s*\(/i',
        '/&#x?[0-9a-f]+;?/i', // HTML entities that could be malicious
    ];

    /**
     * Command injection patterns.
     */
    protected array $commandPatterns = [
        '/;\s*(ls|cat|rm|mv|cp|chmod|chown|wget|curl|bash|sh|python|perl|php|nc|netcat)\b/i',
        '/\|\s*(ls|cat|rm|mv|cp|chmod|chown|wget|curl|bash|sh|python|perl|php|nc|netcat)\b/i',
        '/`[^`]*`/',
        '/\$\([^)]*\)/',
        '/\$\{[^}]*\}/',
        '/>\s*\/?(etc|tmp|var|usr|home)\//i',
        '/\b(system|exec|shell_exec|passthru|popen|proc_open)\s*\(/i',
    ];

    /**
     * Path traversal patterns.
     */
    protected array $pathPatterns = [
        '/\.\.\//',
        '/\.\.\\\\/',
        '/%2e%2e%2f/i',
        '/%2e%2e\//i',
        '/\.\.%2f/i',
        '/%252e%252e%252f/i',
        '/\.\.\%c0\%af/i',
        '/\.\.\%c1\%9c/i',
    ];

    /**
     * Routes to exclude from sanitization (webhooks and payment callbacks).
     */
    protected array $excludedRoutes = [
        'stripe/webhook',
        'payoneer/webhook',
        'escrow/webhook',
        'subscriptions/webhook',
        'checkout/success',
        'checkout/paypal/success',
        'checkout/payoneer/success',
        'wallet/deposit/success',
        'wallet/deposit',
        'escrow/confirm',
        'escrow/cancel',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Setting::get('input_sanitization_enabled', true)) {
            return $next($request);
        }

        // Skip sanitization for excluded routes (webhooks, payment callbacks)
        if ($this->isExcludedRoute($request)) {
            return $next($request);
        }

        $attacks = $this->detectAttacks($request);

        if (!empty($attacks)) {
            return $this->handleAttackDetection($request, $attacks);
        }

        return $next($request);
    }

    /**
     * Detect various attack patterns in the request.
     */
    protected function detectAttacks(Request $request): array
    {
        $attacks = [];
        $inputs = $this->getAllInputs($request);

        foreach ($inputs as $key => $value) {
            if (!is_string($value)) {
                continue;
            }

            // SQL Injection
            if ($this->matchesPatterns($value, $this->sqlPatterns)) {
                $attacks[] = [
                    'type' => 'sql_injection',
                    'field' => $key,
                    'value' => $this->truncateValue($value),
                ];
            }

            // XSS
            if ($this->matchesPatterns($value, $this->xssPatterns)) {
                $attacks[] = [
                    'type' => 'xss',
                    'field' => $key,
                    'value' => $this->truncateValue($value),
                ];
            }

            // Command Injection
            if ($this->matchesPatterns($value, $this->commandPatterns)) {
                $attacks[] = [
                    'type' => 'command_injection',
                    'field' => $key,
                    'value' => $this->truncateValue($value),
                ];
            }

            // Path Traversal
            if ($this->matchesPatterns($value, $this->pathPatterns)) {
                $attacks[] = [
                    'type' => 'path_traversal',
                    'field' => $key,
                    'value' => $this->truncateValue($value),
                ];
            }
        }

        // Check URL path and query string
        $path = urldecode($request->path());
        $query = urldecode($request->getQueryString() ?? '');

        foreach ([$path, $query] as $area) {
            if ($this->matchesPatterns($area, $this->pathPatterns)) {
                $attacks[] = [
                    'type' => 'path_traversal',
                    'field' => 'url',
                    'value' => $this->truncateValue($area),
                ];
            }
        }

        return $attacks;
    }

    /**
     * Get all inputs from request.
     */
    protected function getAllInputs(Request $request): array
    {
        $inputs = [];

        // Query parameters
        foreach ($request->query() as $key => $value) {
            $inputs["query.{$key}"] = $value;
        }

        // Post data
        foreach ($request->post() as $key => $value) {
            if (is_array($value)) {
                $inputs["post.{$key}"] = json_encode($value);
            } else {
                $inputs["post.{$key}"] = $value;
            }
        }

        // Headers (selected)
        $sensitiveHeaders = ['referer', 'user-agent', 'x-forwarded-for'];
        foreach ($sensitiveHeaders as $header) {
            if ($request->hasHeader($header)) {
                $inputs["header.{$header}"] = $request->header($header);
            }
        }

        return $inputs;
    }

    /**
     * Check if value matches any pattern.
     */
    protected function matchesPatterns(string $value, array $patterns): bool
    {
        // Decode potential encoded attacks
        $decoded = html_entity_decode(urldecode($value));

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value) || preg_match($pattern, $decoded)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle detected attack.
     */
    protected function handleAttackDetection(Request $request, array $attacks): Response
    {
        $ip = $request->ip();
        $attackTypes = array_unique(array_column($attacks, 'type'));

        // Log the attack
        Log::channel('security')->alert('Attack detected', [
            'ip' => $ip,
            'path' => $request->path(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
            'attacks' => $attacks,
            'user_id' => auth()->id(),
        ]);

        // Store in database for analysis
        SecurityLog::create([
            'ip_address' => $ip,
            'user_id' => auth()->id(),
            'event_type' => 'attack_detected',
            'severity' => 'critical',
            'description' => 'Attack patterns detected: ' . implode(', ', $attackTypes),
            'metadata' => [
                'attacks' => $attacks,
                'path' => $request->path(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        // Return appropriate error response
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Request blocked due to security policy.',
            ], 403);
        }

        abort(403, 'Request blocked due to security policy.');
    }

    /**
     * Truncate value for logging.
     */
    protected function truncateValue(string $value): string
    {
        return strlen($value) > 200 ? substr($value, 0, 200) . '...' : $value;
    }

    /**
     * Check if the current route should be excluded from sanitization.
     */
    protected function isExcludedRoute(Request $request): bool
    {
        $path = $request->path();

        foreach ($this->excludedRoutes as $route) {
            if ($path === $route || str_starts_with($path, $route . '/')) {
                return true;
            }
        }

        return false;
    }
}
