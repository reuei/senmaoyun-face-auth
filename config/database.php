<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 数据库配置
// +----------------------------------------------------------------------

return [
    // 默认数据库连接
    'default'     => env('database.driver', 'mysql'),

    // 数据库连接配置
    'connections' => [
        'mysql' => [
            'type'            => env('database.type', 'mysql'),
            'hostname'        => env('database.hostname', '127.0.0.1'),
            'database'        => env('database.database', 'senmaoyun'),
            'username'        => env('database.username', 'root'),
            'password'        => env('database.password', ''),
            'hostport'        => env('database.hostport', '3306'),
            'charset'         => 'utf8mb4',
            'prefix'          => env('database.prefix', 'smy_'),
            'debug'           => env('database.debug', true),
            'break_reconnect' => true,
            'params'          => [
                \PDO::ATTR_CASE              => \PDO::CASE_NATURAL,
                \PDO::ATTR_ERRMODE           => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_ORACLE_NULLS      => \PDO::NULL_NATURAL,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
                \PDO::ATTR_EMULATE_PREPARES  => false,
            ],
        ],
    ],
];