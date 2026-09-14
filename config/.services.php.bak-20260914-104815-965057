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
    | a conventional file to locate various service credentials.
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

    'centro_ia' => [
        'url' => env('CENTRO_IA_URL', 'http://vitrine_core_web_hml/api/internal/centro-ia/execute'),
        'token' => env('CENTRO_IA_INTERNAL_TOKEN'),
        'project_id' => env('CENTRO_IA_PROJECT_ID', 'vitrine-ai-social-enterprise'),
        'capability' => env('CENTRO_IA_CAPABILITY', 'social_content_generation'),
        'timeout' => (int) env('CENTRO_IA_TIMEOUT', 30),
    ],

];
