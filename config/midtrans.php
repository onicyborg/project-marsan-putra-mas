<?php

return [
    'server_key' => env('MIDTRANS_IS_PRODUCTION')
        ? env('MIDTRANS_SERVER_KEY_PRODUCTION')
        : env('MIDTRANS_SERVER_KEY_SANDBOX'),
    'client_key' => env('MIDTRANS_IS_PRODUCTION')
        ? env('MIDTRANS_CLIENT_KEY_PRODUCTION')
        : env('MIDTRANS_CLIENT_KEY_SANDBOX'),
    'isProduction' => env('MIDTRANS_IS_PRODUCTION'),
    'isSanitized' => env('MIDTRANS_IS_SANITIZED'),
    'is3ds' => env('MIDTRANS_IS_3DS'),
];
