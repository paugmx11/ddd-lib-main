<?php

declare(strict_types=1);

return [
    'base_url' => env('BACKEND_BASE_URL', 'http://127.0.0.1:8000'),
    'timeout_seconds' => (int) env('BACKEND_TIMEOUT_SECONDS', '10'),
];

