<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Active Payment Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment provider used for KHQR payments.
    | You can override this per-branch later using the payment_providers table.
    |
    | Supported: "cash", "bakong", "third_party_khqr"
    |
    */

    'active_provider' => env('PAYMENT_PROVIDER', 'cash'),

    /*
    |--------------------------------------------------------------------------
    | Payment Provider Configurations
    |--------------------------------------------------------------------------
    |
    | Configure each payment provider's credentials and settings below.
    | Sensitive values should be stored in your .env file.
    |
    */

    'providers' => [

        'bakong' => [
            'base_url' => env('BAKONG_API_URL', 'https://api.bakong.gov.kh'),
            'access_token' => env('BAKONG_ACCESS_TOKEN', ''),
            'merchant_id' => env('BAKONG_MERCHANT_ID', ''),
        ],

        'third_party_khqr' => [
            'base_url' => env('THIRD_PARTY_KHQR_API_URL', ''),
            'api_key' => env('THIRD_PARTY_KHQR_API_KEY', ''),
            'merchant_id' => env('THIRD_PARTY_KHQR_MERCHANT_ID', ''),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | KHQR Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for KHQR QR code generation.
    |
    */

    'khqr' => [
        'default_currency' => env('KHQR_DEFAULT_CURRENCY', 'USD'),
        'expiry_minutes' => env('KHQR_EXPIRY_MINUTES', 15),
        'static_qr_enabled' => env('KHQR_STATIC_QR_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook URLs and verification settings.
    |
    */

    'webhook' => [
        'url' => env('PAYMENT_WEBHOOK_URL', '/api/v1/payments/webhook'),
        'secret' => env('PAYMENT_WEBHOOK_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Behavior
    |--------------------------------------------------------------------------
    |
    | Configure what happens when the primary provider is unavailable.
    |
    */

    'fallback' => [
        'enabled' => env('PAYMENT_FALLBACK_ENABLED', true),
        'provider' => env('PAYMENT_FALLBACK_PROVIDER', 'cash'),
    ],
];
