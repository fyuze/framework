<?php

return [
    'fetch' => 'PDO::FETCH_CLASS',
    'default' => 'sqlite',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'databse' => '',
            'username' => '',
            'password' => '',
            'charset' => '',
            'collation' => '',
            'prefix' => '',
            'strict' => false
        ],
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => 'localhost',
            'prefix' => ''
        ]
    ]
];