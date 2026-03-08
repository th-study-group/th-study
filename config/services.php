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

    'kakao' => [
        'app_key' => env_default('KAKAO_MAP_API_KEY'),
        'map_lat' => env_default('SITE_MAP_LAT', 37.055779),
        'map_lng' => env_default('SITE_MAP_LNG', 129.4282108),
    ],

    'webpush' => [
        'vapid_public_key' => env('VAPID_PUBLIC_KEY'),   // 공개키
        'vapid_private_key' => env('VAPID_PRIVATE_KEY'), // 개인키
        'vapid_subject' => 'mailto:' . env('VAPID_SUBJECT'), // 이메일
    ],

    'ga4' => [
        'measurement_id' => env('GA4_MEASUREMENT_ID'),
    ],

    'adsense' => [
        'id' => env('ADSENSE_ID'),
    ],
];
