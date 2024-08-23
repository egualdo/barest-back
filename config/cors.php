<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'api/admin/*'], //, 'sanctum/csrf-cookie'

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'https://localhost:5173',
        'http://127.0.0.1:8000',
        'https://127.0.0.1:8000',
        'https://localhost:3000',
        'http://localhost:3000',
        'https://admin.barest.es',
        'https://www.admin.barest.es',
        'http://localhost:8000',
        'https://dev.barest.es',
        'https://www.dev.barest.es',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
