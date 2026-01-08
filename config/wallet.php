<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Wallet System Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the wallet system including hold/release mechanism,
    | transaction limits, and idempotency settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Hold Settings
    |--------------------------------------------------------------------------
    |
    | Funds are held during checkout and released/captured on completion.
    | This prevents double-spending and ensures transaction integrity.
    |
    */

    'hold_expiry_hours' => env('WALLET_HOLD_EXPIRY_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Idempotency Settings
    |--------------------------------------------------------------------------
    |
    | Idempotency keys prevent duplicate transactions. Keys are stored
    | and checked before processing any wallet operation.
    |
    */

    'idempotency_ttl_hours' => env('WALLET_IDEMPOTENCY_TTL_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Transaction Limits
    |--------------------------------------------------------------------------
    |
    | Set minimum and maximum limits for wallet operations.
    |
    */

    'min_deposit' => env('WALLET_MIN_DEPOSIT', 5.00),

    'max_balance' => env('WALLET_MAX_BALANCE', 10000.00),

    'min_withdrawal' => env('WALLET_MIN_WITHDRAWAL', 10.00),

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    */

    'default_currency' => env('WALLET_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable detailed logging for wallet transactions.
    |
    */

    'log_transactions' => env('WALLET_LOG_TRANSACTIONS', true),

];
