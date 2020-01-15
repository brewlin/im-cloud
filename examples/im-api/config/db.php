<?php
return [
    'db' => [
        'driver'   => 'mysql',
        'host'     => 'localhost',
        'port'     => 3306,
        'database'  => 'test',
        'username' => 'root',
        'password' => 'roadforhacker',
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