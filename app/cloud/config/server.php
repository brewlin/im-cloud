<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/1 0001
 * Time: 下午 4:08
 */
return [
    'setting' => [
        'daemonize' => (int)env("DAEMONIZE", 0),
        'worker_num' => (int)env("WORKER_NUM", 4),
    ],
    'tcp' => [
        'package_max_length' => 1081920,
        'heartbeat_idle_time' => 20,
        'heartbeat_check_interval' => 5,
    ],
    'scan' => [
    ]

];