<?php
return [
    'default' => env('database.driver', 'mysql'),
    'connections' => [
        'mysql' => [
            'type'      => 'mysql',
            'hostname'  => env('database.hostname', '127.0.0.1'),
            'database'  => env('database.database', 'senmaoyun'),
            'username'  => env('database.username', 'root'),
            'password'  => env('database.password', ''),
            'hostport'  => env('database.hostport', '3306'),
            'charset'   => 'utf8mb4',
            'prefix'    => env('database.prefix', 'smy_'),
            'debug'     => true,
            'params'    => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],
    ],
];