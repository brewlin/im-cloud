<?php
return [
    'db' => [
        'driver'   => 'mysql',
        'host'     => 'localhost',
        'port'     => 3306,
        'dsn'      => 'mysql:dbname=dbname;host=127.0.0.1:3306',
        'database'  => 'test',
        'username' => 'test',
        'password' => 'test',
        'prefix'   => '',
        'options'  => [
            'charset'  => 'utf8mb4',
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            'collation' => 'utf8mb4_unicode_ci',
        ],
        'config'   => [
            'strict'    => true,
            'timezone'  => '+8:00',
            'modes'     => 'NO_ENGINE_SUBSTITUTION,STRICT_TRANS_TABLES',
            'fetchMode' => PDO::FETCH_ASSOC
        ]
    ]
];