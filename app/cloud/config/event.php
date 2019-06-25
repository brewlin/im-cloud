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

return [
    //监听onpipmessage事件
    SwooleEvent::PIPE_MESSAGE => new PipeMessageListener(),
];