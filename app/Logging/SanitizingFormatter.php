<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;

class SanitizingFormatter extends LineFormatter
{
    /**
     * Patterns to match and replace sensitive data in final output.
     */
    protected array $patterns = [
        // Credit card numbers (with or without spaces/dashes)
        '/\b(?:\d{4}[\s-]?){3}\d{4}\b/' => '[CARD-XXXX]',

        // CVV/CVC codes
        '/\b(cvv|cvc|cvv2|cvc2|security.?code)["\s:=>\']*([\d]{3,4})\b/i' => '$1:[CVV]',

        // SSN (US Social Security Numbers)
        '/\b\d{3}[-\s]?\d{2}[-\s]?\d{4}\b/' => '[SSN]',

        // Email addresses
        '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/' => '[EMAIL]',

        // Phone numbers
        '/\b(?:\+?1[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}\b/' => '[PHONE]',

        // Password values in JSON/array format: "password":"value" or 'password':'value'
        '/"password"\s*:\s*"[^"]*"/i' => '"password":"[REDACTED]"',
        '/"passwd"\s*:\s*"[^"]*"/i' => '"passwd":"[REDACTED]"',
        '/"secret"\s*:\s*"[^"]*"/i' => '"secret":"[REDACTED]"',
        '/"token"\s*:\s*"[^"]*"/i' => '"token":"[REDACTED]"',
        '/"api_key"\s*:\s*"[^"]*"/i' => '"api_key":"[REDACTED]"',
        '/"apikey"\s*:\s*"[^"]*"/i' => '"apikey":"[REDACTED]"',
        '/"api_secret"\s*:\s*"[^"]*"/i' => '"api_secret":"[REDACTED]"',
        '/"access_token"\s*:\s*"[^"]*"/i' => '"access_token":"[REDACTED]"',
        '/"refresh_token"\s*:\s*"[^"]*"/i' => '"refresh_token":"[REDACTED]"',
        '/"authorization"\s*:\s*"[^"]*"/i' => '"authorization":"[REDACTED]"',
        '/"auth_token"\s*:\s*"[^"]*"/i' => '"auth_token":"[REDACTED]"',
        '/"private_key"\s*:\s*"[^"]*"/i' => '"private_key":"[REDACTED]"',
        '/"credit_card"\s*:\s*"[^"]*"/i' => '"credit_card":"[REDACTED]"',
        '/"card_number"\s*:\s*"[^"]*"/i' => '"card_number":"[REDACTED]"',
        '/"cvv"\s*:\s*"[^"]*"/i' => '"cvv":"[REDACTED]"',
        '/"cvc"\s*:\s*"[^"]*"/i' => '"cvc":"[REDACTED]"',
        '/"ssn"\s*:\s*"[^"]*"/i' => '"ssn":"[REDACTED]"',
        '/"bank_account"\s*:\s*"[^"]*"/i' => '"bank_account":"[REDACTED]"',
        '/"account_number"\s*:\s*"[^"]*"/i' => '"account_number":"[REDACTED]"',
        '/"routing_number"\s*:\s*"[^"]*"/i' => '"routing_number":"[REDACTED]"',
        '/"stripe_key"\s*:\s*"[^"]*"/i' => '"stripe_key":"[REDACTED]"',
        '/"stripe_secret"\s*:\s*"[^"]*"/i' => '"stripe_secret":"[REDACTED]"',
        '/"webhook_secret"\s*:\s*"[^"]*"/i' => '"webhook_secret":"[REDACTED]"',

        // Stripe keys pattern
        '/sk_(?:live|test)_[A-Za-z0-9]+/' => '[STRIPE-KEY]',
        '/pk_(?:live|test)_[A-Za-z0-9]+/' => '[STRIPE-PK]',

        // Bearer tokens
        '/Bearer\s+[A-Za-z0-9\-._~+\/]+=*/i' => 'Bearer [TOKEN]',

        // Generic long alphanumeric tokens (API keys, secrets)
        '/"[^"]*(?:key|secret|token)[^"]*"\s*:\s*"[A-Za-z0-9_\-]{32,}"/i' => '"$1":"[LONG-TOKEN]"',
    ];

    /**
     * Format the log record with PII sanitization.
     */
    public function format(LogRecord $record): string
    {
        // Let parent format the record first
        $output = parent::format($record);

        // Apply pattern-based sanitization to the final output
        return $this->sanitize($output);
    }

    /**
     * Apply all sanitization patterns to a string.
     */
    protected function sanitize(string $value): string
    {
        foreach ($this->patterns as $pattern => $replacement) {
            $result = @preg_replace($pattern, $replacement, $value);
            if ($result !== null) {
                $value = $result;
            }
        }

        return $value;
    }

    /**
     * Add a custom pattern for sanitization.
     */
    public function addPattern(string $pattern, string $replacement): self
    {
        $this->patterns[$pattern] = $replacement;
        return $this;
    }
}
