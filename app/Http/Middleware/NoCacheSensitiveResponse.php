<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoCacheSensitiveResponse
{
    /**
     * Prevent caching of sensitive pages.
     *
     * This middleware adds headers to prevent browsers and proxies from
     * caching sensitive pages like checkout, wallet, and account pages.
     * This protects user data from being exposed via browser back button
     * or shared computers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Set headers to prevent caching
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}
