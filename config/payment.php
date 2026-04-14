<?php

return [
    'qr_expire_minutes' => (int) env('PAYMENT_QR_EXPIRE_MINUTES', 5),

    'bank' => [
        'bin' => env('BANK_BIN', '970422'),
        'account_no' => env('BANK_ACCOUNT_NO'),
        'account_name' => env('BANK_ACCOUNT_NAME'),
    ],

    'vietqr' => [
        'template' => env('QR_TEMPLATE', 'compact'),
        'client_id' => env('VIETQR_CLIENT_ID'),
        'api_key' => env('VIETQR_API_KEY'),
        'generate_url' => env('VIETQR_GENERATE_URL', 'https://api.vietqr.io/v2/generate'),
        'image_url' => env('VIETQR_IMAGE_URL', 'https://api.vietqr.io/image'),
    ],

    'sepay' => [
        'webhook_api_key' => env('SEPAY_WEBHOOK_API_KEY', env('SEPAY_WEBHOOK_SECRET')),
        'api_token' => env('SEPAY_API_TOKEN'),
        'api_base_url' => env('SEPAY_API_BASE_URL', 'https://userapi.sepay.vn/v2'),
    ],
];
