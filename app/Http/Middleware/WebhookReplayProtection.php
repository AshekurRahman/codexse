<?php

namespace App\Http\Middleware;

use App\Services\WebhookProtectionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WebhookReplayProtection
{
    public function __construct(
        protected WebhookProtectionService $webhookProtection
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $provider  The webhook provider (stripe, payoneer, generic)
     */
    public function handle(Request $request, Closure $next, string $provider = 'generic'): Response
    {
        // Get timestamp and nonce headers based on provider
        $timestampHeader = $this->getTimestampHeader($provider);
        $nonceHeader = $this->getNonceHeader($provider);

        $timestamp = $timestampHeader ? $request->header($timestampHeader) : null;
        $nonce = $nonceHeader ? $request->header($nonceHeader) : null;

        // For providers without nonce headers, generate from payload
        if (!$nonce) {
            $nonce = hash('sha256', $provider . ':' . $request->getContent());
        }

        // Validate timestamp if present
        if ($timestamp && !$this->webhookProtection->validateTimestamp($timestamp)) {
            Log::warning('Webhook replay protection: expired timestamp', [
                'provider' => $provider,
                'timestamp' => $timestamp,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'error' => 'Webhook expired',
                'message' => 'The webhook timestamp indicates this request is too old.',
            ], 400);
        }

        // Validate nonce (check for duplicates)
        if (!$this->webhookProtection->validateNonce($nonce, $provider)) {
            Log::warning('Webhook replay protection: duplicate detected', [
                'provider' => $provider,
                'nonce' => substr($nonce, 0, 32) . '...',
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'error' => 'Duplicate webhook',
                'message' => 'This webhook has already been processed.',
            ], 400);
        }

        // Store request info for use in controller
        $request->attributes->set('webhook_provider', $provider);
        $request->attributes->set('webhook_nonce', $nonce);
        $request->attributes->set('webhook_timestamp', $timestamp);

        // Process the webhook
        $response = $next($request);

        // If successful, register the nonce
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $this->webhookProtection->registerNonce(
                $nonce,
                $provider,
                $request->attributes->get('webhook_event_type'),
                $timestamp ? \Carbon\Carbon::createFromTimestamp((int)$timestamp) : null,
                [],
                $request->ip()
            );
        }

        return $response;
    }

    /**
     * Get the timestamp header name for a provider.
     */
    protected function getTimestampHeader(string $provider): ?string
    {
        return match ($provider) {
            'stripe' => null, // Stripe handles this in signature validation
            'payoneer' => 'X-Payoneer-Timestamp',
            default => 'X-Webhook-Timestamp',
        };
    }

    /**
     * Get the nonce header name for a provider.
     */
    protected function getNonceHeader(string $provider): ?string
    {
        return match ($provider) {
            'stripe' => null, // We use event ID from payload
            'payoneer' => 'X-Payoneer-Request-Id',
            default => 'X-Webhook-Nonce',
        };
    }
}
