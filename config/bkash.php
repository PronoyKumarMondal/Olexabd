<?php

return [
    'sandbox' => env('BKASH_SANDBOX', true),
    'base_url' => env('BKASH_SANDBOX', true) 
        ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta' 
        : 'https://tokenized.pay.bka.sh/v1.2.0-beta',
    'app_key' => env('BKASH_APP_KEY', 'demo_app_key'),
    'app_secret' => env('BKASH_APP_SECRET', 'demo_app_secret'),
    'username' => env('BKASH_USERNAME', 'demo_user'),
    'password' => env('BKASH_PASSWORD', 'demo_pass'),
];
