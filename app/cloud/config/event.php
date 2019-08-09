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
use \App\Websocket\MessageListener;
use \App\Websocket\HandshakeListener;
use App\Tcp\ReceiveListener;
use App\Event\OnCloseListener;
use App\Event\WorkerStartListener;

return [
    //监听onpipmessage事件
    SwooleEvent::PIPE_MESSAGE => new PipeMessageListener(),

    //监听进程启动事件
    SwooleEvent::WORKER_START => new WorkerStartListener(),

    //监听进程关闭事件
    SwooleEvent::WORKER_STOP  => new WorkerStopListener(),
    SwooleEvent::SHUTDOWN     => new ShutdownListener(),

    //监听tcp事件
    SwooleEvent::RECEIVE      => new ReceiveListener(),

    //监听websocket 事件
    SwooleEvent::MESSAGE      => new MessageListener(),
    //websocket握手事件
    SwooleEvent::HANDSHAKE    => new HandshakeListener(),

    //server监听关闭连接事件然后grpc通知logic销毁连接信息
    SwooleEvent::CLOSE        => new OnCloseListener(),
];