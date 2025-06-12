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

    'kprimepay' => [
        'merchant_number' => env('KPRIMEPAY_MERCHANT_NUMBER'),
        'secret_key' => env('KPRIMEPAY_SECRET_KEY'),
        'api_url' => env('KPRIMEPAY_API_URL'),

        'name_seeder'=> env('NAME_SEEDER'),
        'sms_api_url' => env('KPRIMEPAY_API_URL_SMS'),
        'sms_token' => env('KPRIMEPAY_SMS_TOKEN'),
        'sms_key' => env('KPRIMEPAY_SMS_KEY'),
    ],

];
