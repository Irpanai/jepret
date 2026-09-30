<?php

return [
    'environment' => env('DOKU_ENV', 'sandbox'),
    'client_id' => env('DOKU_CLIENT_ID'),
    'client_secret' => env('DOKU_CLIENT_SECRET'),
    'merchant_id' => env('DOKU_MERCHANT_ID'),
    'terminal_id' => env('DOKU_TERMINAL_ID'),
    'private_key' => env('DOKU_PRIVATE_KEY'),
    'private_key_path' => env('DOKU_PRIVATE_KEY_PATH'),
    'postal_code' => env('DOKU_POSTAL_CODE'),
    'fee_type' => (int) env('DOKU_FEE_TYPE', 1),
    'qris_ttl_minutes' => (int) env('DOKU_QRIS_TTL_MINUTES', 60),
    'base_urls' => [
        'sandbox' => 'https://api-sandbox.doku.com',
        'production' => 'https://api.doku.com',
    ],
];
