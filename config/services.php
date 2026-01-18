<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'daraja' => [
        'consumer_key' => env('DARAJA_CONSUMER_KEY'),
        'consumer_secret' => env('DARAJA_CONSUMER_SECRET'),
        'shortcode' => env('DARAJA_SHORTCODE'),
        'passkey' => env('DARAJA_PASSKEY'),
        'base_url' => env('DARAJA_BASE_URL', 'https://sandbox.safaricom.co.ke'),
        'callback_url' => env('DARAJA_CALLBACK_URL'), // Set in .env or will be generated in DarajaService
        'environment' => env('DARAJA_ENVIRONMENT', 'sandbox'), // sandbox or production
    ],

    'n8n' => [
        'enabled' => env('N8N_ENABLED', false),
        'webhook_url' => env('N8N_WEBHOOK_URL'),
        'timeout' => env('N8N_TIMEOUT', 10),
        'api_key' => env('N8N_API_KEY', 'your-secret-api-key-here'),
    ],

];
