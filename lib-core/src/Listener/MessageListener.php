<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/15
 * Time: 23:43
 */

namespace Core\Listener;


use Core\Swoole\MessageInterface;
use Swoole\Websocket\Frame;
use Swoole\Websocket\Server;

class MessageListener implements MessageInterface
{
    public function onMessage(Server $server, Frame $frame): void
    {
        var_dump(" websocket receive");
        var_dump($server,$frame);
        // TODO: Implement onMessage() method.
    }

}