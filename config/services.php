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

    'google' => (function () {
        $local = [];
        $localPath = __DIR__ . '/google.local.php';

        if (is_file($localPath)) {
            $local = require $localPath;
        }

        return [
            'client_id' => env('GOOGLE_CLIENT_ID') ?: ($local['client_id'] ?? ''),
            'client_secret' => env('GOOGLE_CLIENT_SECRET') ?: ($local['client_secret'] ?? ''),
            'redirect' => env('GOOGLE_REDIRECT_URI') ?: ($local['redirect'] ?? env('APP_URL', 'http://127.0.0.1:8000') . '/auth/google/callback'),
        ];
    })(),

];
