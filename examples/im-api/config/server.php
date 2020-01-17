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
        'http_parse_post' => true,
    ],
    'scan' => [
    ],
];