<?php

return [

    'enabled' => env('AUTO_PRINT_ENABLED', true),


    'printers' => [
        'name' => env('PRINTER_NAME', 'default'),
        'type' => 'network',
        'enabled' => true,
        'ip' => env('PRINTER_IP', '127.0.0.1'),
        'port' => 9100,
        'copies' => 1,
        'temp_path' => storage_path('app/temp/printing'),
        'cleanup_after_hours' => 24,
        'job_timeout' => 120,
        'max_retries' => 3,
    ]
];
