<?php

return [
    'default' => env('DB_CONNECTION', 'wordpress'),

    'connections' => [
        'wordpress' => [
            'driver' => 'mysql',
            'host' =>  env('DB_HOST') ?? DB_HOST,
            'database' => env('DB_NAME') ?? DB_NAME,
            'username' => env('DB_USER') ?? DB_USER,
            'password' => env('DB_PASSWORD') ?? DB_PASSWORD,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'prefix' => env('TABLE_PREFIX') ?? TABLE_PREFIX,
            'strict' => true,
            'engine' => null,
        ],
    ],

    'migrations' => 'migrations',
];
