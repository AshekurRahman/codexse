<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'seller' => \App\Http\Middleware\IsSeller::class,
        ]);

        // Exclude Stripe webhook from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log HTTP errors (403, 404, 419, 500, 503, etc.)
        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            $statusCode = $e->getStatusCode();
            $errorTypes = [
                403 => 'Access Denied',
                404 => 'Page Not Found',
                419 => 'Session Expired',
                500 => 'Server Error',
                503 => 'Service Unavailable',
            ];

            $errorType = $errorTypes[$statusCode] ?? 'HTTP Error';

            Log::channel('errors')->warning("[$statusCode] $errorType", [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => $request->user()?->id,
                'referer' => $request->header('referer'),
            ]);

            return null; // Return null to use default error page rendering
        });
    })->create();
