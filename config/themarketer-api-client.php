<?php

declare(strict_types=1);

return [
    'customerId' => env('THEMARKETER_CUSTOMER_ID', ''),
    'restKey' => env('THEMARKETER_REST_KEY', ''),
    'trackingKey' => env('THEMARKETER_TRACKING_KEY', ''),
    'restUrl' => env('THEMARKETER_REST_URL', 'https://t.themarketer.com'),
    'trackingUrl' => env('THEMARKETER_TRACKING_URL', 'https://t.themarketer.com'),
    'maxRetryAttempts' => (int) env('THEMARKETER_MAX_RETRY_ATTEMPTS', 1),
];
