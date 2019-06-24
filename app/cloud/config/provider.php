<?php

return [
    'consul' => [
        'address' => '192.168.199.184',
        'port'    => 8500,
        'register' => [
            'ID'                => '',
            'Name'              => 'im-cloud-node',
            'Tags'              => [],
            'enableTagOverride'=> false,
            'Address'           => '192.168.199.103',
            'Port'              => 8000,
            'Check'             => [
                'id'       => '',
                'name'     => '',
                'tcp'      => '192.168.199.184:8099',
                'interval' => 10,
                'timeout'  => -1,
            ],
        ],
        'discovery' => [
            'name' => 'im-logic',
            'dc' => 'dc1',
            'near' => '',
            'tag' =>'',
            'passing' => true
        ]
    ],
];