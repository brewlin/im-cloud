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
        'http_parse_post' => true,
        'task_worker_num' => (int)env("TASK_WORKER_NUM"),
        'task_enable_coroutine' => env("TASK_ENABLE_COROUTINE",false),
    ],
    'scan' => [
    ]
];