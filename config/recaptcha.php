<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Google reCAPTCHA v3 integration.
    | Get your keys from: https://www.google.com/recaptcha/admin/create
    |
    */

    'enabled' => env('RECAPTCHA_ENABLED', true),

    'site_key' => env('RECAPTCHA_SITE_KEY'),

    'secret_key' => env('RECAPTCHA_SECRET_KEY'),

    'threshold' => env('RECAPTCHA_THRESHOLD', 0.5),

    'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
];
