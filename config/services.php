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

    'paypal' => [
        'base_uri' => env('PAYPAL_BASE_URI'),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'plans' => [
            '1-mensual' => env('PAYPAL_EM_PLAN'),
            '1-trimestral' => env('PAYPAL_ET_PLAN'),
            '1-semestral' => env('PAYPAL_ES_PLAN'),
            '2-mensual' => env('PAYPAL_AM_PLAN'),
            '2-trimestral' => env('PAYPAL_AT_PLAN'),
            '2-semestral' => env('PAYPAL_AS_PLAN'),
            '3-mensual' => env('PAYPAL_PM_PLAN'),
            '3-trimestral' => env('PAYPAL_PT_PLAN'),
            '3-semestral' => env('PAYPAL_PS_PLAN'),
            '4-mensual' => env('PAYPAL_PPM_PLAN'),
            '4-trimestral' => env('PAYPAL_PPT_PLAN'),
            '4-semestral' => env('PAYPAL_PPS_PLAN'),
        ],
    ],

    'stripe' => [
        'base_uri' => env('STRIPE_BASE_URI'),
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'class' => App\Services\StripeService::class,
        'plans' => [
            '1-mensual' => env('STRIPE_EM_PLAN'),
            '1-trimestral' => env('STRIPE_ET_PLAN'),
            '1-semestral' => env('STRIPE_ES_PLAN'),
            '2-mensual' => env('STRIPE_AM_PLAN'),
            '2-trimestral' => env('STRIPE_AT_PLAN'),
            '2-semestral' => env('STRIPE_AS_PLAN'),
            '3-mensual' => env('STRIPE_PM_PLAN'),
            '3-trimestral' => env('STRIPE_PT_PLAN'),
            '3-semestral' => env('STRIPE_PS_PLAN'),
            '4-mensual' => env('STRIPE_PPM_PLAN'),
            '4-trimestral' => env('STRIPE_PPT_PLAN'),
            '4-semestral' => env('STRIPE_PPS_PLAN'),
        ],
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_ID'),
        'client_secret' => env('FACEBOOK_SECRET'),
        //'redirect' => config('app.url') . '/auth/facebook/callback'
        'redirect' => '/login/facebook/callback'
    ],

    'google' => [
        'client_id' => env('GOOGLE_ID'),
        'client_secret' => env('GOOGLE_SECRET'),
        'redirect' => 'https://back.barest.es/login/google/callback'
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
