<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/25 0025
 * Time: 下午 2:11
 */

/**
 * set event to base swoole
 * 给swoole 设置基础的监听事件，
 */
use \Core\Swoole\SwooleEvent;
use \App\Event\PipeMessageListener;
use \App\Event\WorkerStopListener;
use \App\Event\ShutdownListener;
use \App\Event\MessageListener;
use \App\Event\WorkerStartListener;

return [
    //监听onpipmessage事件
    SwooleEvent::PIPE_MESSAGE => new PipeMessageListener(),
    SwooleEvent::WORKER_STOP => new WorkerStopListener(),
    SwooleEvent::SHUTDOWN    => new ShutdownListener(),
    SwooleEvent::WORKER_START => new WorkerStartListener()
];