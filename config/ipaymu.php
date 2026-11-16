<?php

return [
    'api_key' => env('IPAYMU_API_KEY'),
    'va' => env('IPAYMU_VA'),
    'api_url' => env('IPAYMU_API_URL'),
    'url' => [
        'return' => env('IPAYMU_RETURN_URL'),
        'notify' => env('IPAYMU_NOTIFY_URL'),
        'cancel' => env('IPAYMU_CANCEL_URL'),
    ],
];