<?php


// ============================================
// 1. CONFIG FILE: config/printing.php
// ============================================

return [
    'enabled' => env('AUTO_PRINT_ENABLED', true),

    'default_printer' => env('DEFAULT_PRINTER', 'default'),

    // Store-specific printer configurations
    'printers' => [
        1 => [
            'name' => 'NP15A337',
            'type' => 'network',
            'enabled' => true,

            // Printer TCP/IP settings
            'ip' => '192.168.1.45',
            'port' => 9100,

            // Print behavior
            'copies' => 1,
        ],

        2 => [
            'name' => 'Canon Store 2',
            'type' => 'network',
            'enabled' => true,
            'ip' => '192.168.1.101',
            'port' => 9100,
            'copies' => 1,
        ],
    ],

    // Fallback default printer
    'default' => [
        'name' => 'Office Printer',
        'type' => 'network',
        'enabled' => true,
        'ip' => '192.168.1.200',
        'port' => 9100,
        'copies' => 1,
    ],

    // Print settings
    'temp_path' => storage_path('app/temp/printing'),
    'cleanup_after_hours' => 24,
    'job_timeout' => 120,
    'max_retries' => 3,
];
