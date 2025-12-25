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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Crypto API Services
    'coingecko' => [
        'url' => env('COINGECKO_API_URL', 'https://api.coingecko.com/api/v3'),
    ],

    'whale_alert' => [
        'url' => env('WHALE_ALERT_API_URL', 'https://api.whale-alert.io/v1'),
        'key' => env('WHALE_ALERT_API_KEY'),
    ],

    'fear_greed' => [
        'url' => env('FEAR_GREED_API_URL', 'https://api.alternative.me/fng'),
    ],

    'gold' => [
        'url' => env('GOLD_API_URL', 'https://api.frankfurter.app'),
    ],

    'metals' => [
        'key' => env('METALS_API_KEY', 'demo'),
    ],

];
