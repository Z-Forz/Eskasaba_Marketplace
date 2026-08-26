<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'whatsapp' => [
        'enabled'      => env('WA_ENABLE_NOTIFICATION', true),
        'url'          => env('WA_GATEWAY_URL', 'http://localhost:3000/send-message'),
        'token'        => env('WA_GATEWAY_TOKEN', ''),
        'admin_number' => env('WA_ADMIN_NUMBER', '081234567890'),
    ],

    'school_api' => [
        'url'   => env('SCHOOL_API_URL', 'https://api.sekolah.example'),
        'token' => env('SCHOOL_API_TOKEN', ''),
    ],

];
