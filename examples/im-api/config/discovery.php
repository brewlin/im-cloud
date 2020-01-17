<?php

return [
    'consul' => [
        'address' => env("DISCOVERY_ADDRESS","127.0.0.1"),
        'port'    => env("DISCOVERY_PORT","8500"),
        'register' => [
            'ID'                => '',
            //只注册了grpc 服务，其他都是私有的
            //tcp 和websocket   通过nginx负载均衡即可
            'Name'              => "grpc-im-logic-node",
            'Tags'              => [],
            'enableTagOverride'=> false,
            'Address'           => '127.0.0.1',
            'Port'              => (int)env("HTTP_PORT",9600),
            'Check'             => [
                'id'       => '',
                'name'     => '',
                'http'      => "http://127.0.0.1:".env('DISCOVERY_CHECK_PORT',9600)."/health",
                'interval' => "10s",
                'timeout'  => "10s",
            ],
        ],
        'discovery' => [
            'name' => 'grpc-im-logic-node',
            'dc' => 'dc1',
            'near' => '',
            'tag' =>'',
            'passing' => true
        ]
    ],
];