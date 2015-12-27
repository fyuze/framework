<?php
return [
    'debug' => true,
    'name' => 'Fyuze',
    'timezone' => 'UTC',
    'charset' => 'UTF-8',
    'services' => [
        'Fyuze\Kernel\Services\Database',
        'Fyuze\Kernel\Services\Logger',
        'Fyuze\Kernel\Services\Event',
        'Fyuze\Kernel\Services\Debug'
    ],
    'modules' => [

    ],
    'error_handler' => [
        'log_errors' => true,
        'log_prefix' => 'fyuze_',
        'log_frequency' => 'daily'
    ]
];
