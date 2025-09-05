<?php

return [
    'default' => env('DB_CONNECTION', 'wordpress'),

    'connections' => [
        'wordpress' => [
            'driver' => 'mysql',
            'host' => DB_HOST,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'prefix' => 'fmr_',
            'strict' => true,
            'engine' => null,
        ],
    ],

    'migrations' => 'migrations',
];
