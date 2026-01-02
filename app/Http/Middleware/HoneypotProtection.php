<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HoneypotProtection
{
    /**
     * Honeypot field names (hidden fields that bots tend to fill).
     */
    protected array $honeypotFields = [
        'website',
        'url',
        'phone_number',
        'fax',
        'company_website',
        'hp_field', // Generic honeypot
    ];

    /**
     * Minimum time in seconds for form submission (too fast = bot).
     */
    protected int $minSubmissionTime = 2;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check POST/PUT/PATCH requests
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            return $next($request);
        }

        // Skip if honeypot is disabled
        if (!Setting::get('honeypot_enabled', true)) {
            return $next($request);
        }

        // Skip for AJAX/API requests without honeypot fields
        if ($request->expectsJson() && !$this->hasHoneypotFields($request)) {
            return $next($request);
        }

        // Check honeypot fields
        if ($this->honeypotTriggered($request)) {
            return $this->handleBotDetection($request, 'honeypot_filled');
        }

        // Check timestamp (form submitted too quickly)
        if ($this->submittedTooQuickly($request)) {
            return $this->handleBotDetection($request, 'submitted_too_quickly');
        }

        // Remove honeypot fields from request before passing to controller
        $this->cleanHoneypotFields($request);

        return $next($request);
    }

    /**
     * Check if request has honeypot fields.
     */
    protected function hasHoneypotFields(Request $request): bool
    {
        foreach ($this->honeypotFields as $field) {
            if ($request->has($field)) {
                return true;
            }
        }

        return $request->has('_hp_timestamp');
    }

    /**
     * Check if honeypot was triggered.
     */
    protected function honeypotTriggered(Request $request): bool
    {
        foreach ($this->honeypotFields as $field) {
            $value = $request->input($field);
            if (!empty($value)) {
                Log::channel('security')->info('Honeypot triggered', [
                    'field' => $field,
                    'value' => substr($value, 0, 100),
                    'ip' => $request->ip(),
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Check if form was submitted too quickly.
     */
    protected function submittedTooQuickly(Request $request): bool
    {
        $timestamp = $request->input('_hp_timestamp');

        if (!$timestamp) {
            return false;
        }

        try {
            $formLoadTime = (int) decrypt($timestamp);
            $submissionTime = time() - $formLoadTime;

            if ($submissionTime < $this->minSubmissionTime) {
                Log::channel('security')->info('Form submitted too quickly', [
                    'submission_time' => $submissionTime,
                    'min_time' => $this->minSubmissionTime,
                    'ip' => $request->ip(),
                ]);
                return true;
            }
        } catch (\Exception $e) {
            // Invalid timestamp - could be tampering
            Log::channel('security')->warning('Invalid honeypot timestamp', [
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
            ]);
            return true;
        }

        return false;
    }

    /**
     * Handle detected bot.
     */
    protected function handleBotDetection(Request $request, string $reason): Response
    {
        $ip = $request->ip();

        // Increment bot detection counter
        $key = "bot_detection:{$ip}";
        $count = Cache::increment($key);

        if ($count === 1) {
            Cache::put($key, 1, now()->addHour());
        }

        Log::channel('security')->warning('Bot detected', [
            'ip' => $ip,
            'reason' => $reason,
            'path' => $request->path(),
            'detection_count' => $count,
        ]);

        // Auto-block if threshold exceeded
        $threshold = Setting::get('bot_block_threshold', 5);
        if ($count >= $threshold) {
            BlockedIp::updateOrCreate(
                ['ip_address' => $ip],
                [
                    'reason' => 'Bot activity detected: ' . $reason,
                    'blocked_by' => 'honeypot',
                    'is_active' => true,
                    'expires_at' => now()->addHours(24),
                ]
            );

            Cache::forget("blocked_ip:{$ip}");
        }

        // Return 422 to appear like validation error
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => ['_token' => ['Please try again.']],
            ], 422);
        }

        return redirect()->back()
            ->withInput()
            ->withErrors(['_token' => 'Please try again.']);
    }

    /**
     * Remove honeypot fields from request.
     */
    protected function cleanHoneypotFields(Request $request): void
    {
        $request->request->remove('_hp_timestamp');

        foreach ($this->honeypotFields as $field) {
            $request->request->remove($field);
        }
    }
}
