<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/1 0001
 * Time: 下午 4:08
 */
return [
    //default process
    'server' => env("SERVER",\Core\Cloud::Process),
    'setting' => [
        'daemonize' => (int)env("DAEMONIZE", 0),
    ],
    'scan' => [

    ]

];