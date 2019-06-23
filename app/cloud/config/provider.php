<?php

/*
 * This file is part of Swoft.
 * (c) Swoft <group@swoft.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'consul' => [
        'address' => '192.168.199.184',
        'port'    => 8500,
        'register' => [
            'id'                => '',
            'name'              => 'im-cloud',
            'tags'              => [],
            'enableTagOverride' => false,
            'service'           => [
                'address' => '192.168.199.103',
                'port'   => '8000',
            ],
            'check'             => [
                'id'       => '',
                'name'     => '',
                'tcp'      => '192.168.199.184:8099',
                'interval' => 10,
                'timeout'  => 1,
            ],
        ],
        'discovery' => [
            'name' => 'im-logic',
            'dc' => 'dc',
            'near' => '',
            'tag' =>'',
            'passing' => true
        ]
    ],
];