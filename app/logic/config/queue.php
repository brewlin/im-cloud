<?php
return [
    "type" => "amqp",
    "host" => "localhost",
    "port" => 5672,
    "user" => "guest",
    "password" => "guest",
    'vhost' => '/',
    'params' => [
        'insist' => false,
        'login_method' => 'AMQPLAIN',
        'login_response' => null,
        'locale' => 'en_US',
        'connection_timeout' => 3.0,
        //此参数针对logic节点性能至关重要，生产数据后无需获得结果
        'read_write_timeout' => 0.1,
        'context' => null,
        'keepalive' => false,
        'heartbeat' => 3,
    ],
];