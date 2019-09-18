<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/26
 * Time: 21:31
 */

namespace App\Websocket;


use App\Packet\Packet;
use App\Service\Service\Dispatcher;
use Core\Context\Context;
use Core\Swoole\MessageInterface;
use Log\Helper\Log;
use Swoole\Websocket\Frame;
use Swoole\Websocket\Server;

/**
 * websocket 接收事件入口
 * Class MessageListener
 * @package App\Event
 */
class MessageListener implements MessageInterface
{
    /**
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server, Frame $frame): void
    {
        Log::info("fd:{$frame->fd} data:{$frame->data}");
        try {
            /** @var Packet $packet */
            $packet = bean(Packet::class)->unpack($frame->data);
            Context::withValue(Packet::class,$packet);
            Context::withValue("fd",$frame->fd);

            //dispatch
            container()->get(Dispatcher::class)
                       ->dispatch();
        } catch (\Throwable $e) {
            $file = $e->getFile();
            $line = $e->getLine();
            $code = $e->getCode();
            $exception = $e->getMessage();
            Log::error("file:".$file." line:$line code:$code msg:$exception");
            $server->close($frame->fd);
        }
        //destory context
        Context::compelete();
    }

}