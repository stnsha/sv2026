<?php

return [
    'environment' => env('APP_ENV', 'production') === 'production' ? 'production' : 'dev',

    'environments' => [
        'dev' => [
            'base_url'      => 'https://dev.toyyibpay.com',
            'secret_key'    => env('DEV_TOYYIB_PAY_SECRET_KEY'),
            'category_code' => env('DEV_TOYYIB_PAY_CATEGORY_CODE'),
        ],
        'production' => [
            'base_url'      => 'https://toyyibpay.com',
            'secret_key'    => env('TOYYIB_PAY_SECRET_KEY'),
            'category_code' => env('TOYYIB_PAY_CATEGORY_CODE'),
        ],
    ],
];
