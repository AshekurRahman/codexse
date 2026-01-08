<?php

if (!function_exists('csp_nonce')) {
    /**
     * Get the CSP nonce for the current request.
     *
     * Usage in templates:
     *   <script nonce="{{ csp_nonce() }}">...</script>
     *   <style nonce="{{ csp_nonce() }}">...</style>
     *
     * @return string The nonce value or empty string if not set
     */
    function csp_nonce(): string
    {
        return request()->attributes->get('cspNonce', '');
    }
}

if (!function_exists('csp_nonce_attr')) {
    /**
     * Get the full nonce attribute for HTML tags.
     *
     * Usage in templates:
     *   <script {!! csp_nonce_attr() !!}>...</script>
     *
     * @return string The nonce attribute (e.g., 'nonce="abc123"') or empty string
     */
    function csp_nonce_attr(): string
    {
        $nonce = csp_nonce();
        return $nonce ? "nonce=\"{$nonce}\"" : '';
    }
}
