<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Keys
    |--------------------------------------------------------------------------
    |
    | The Stripe publishable key and secret key give you access to Stripe's
    | API. These are fallback values from .env - the application will check
    | the database settings first (configured via Admin Panel).
    |
    */

    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency that will be used when generating charges
    | from your application. You may set this to any currency supported by
    | Stripe: https://stripe.com/docs/currencies
    |
    */

    'currency' => env('STRIPE_CURRENCY', 'usd'),
];
