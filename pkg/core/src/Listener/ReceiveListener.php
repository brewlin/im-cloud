<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/16
 * Time: 10:24
 */

namespace Core\Listener;


use Core\Swoole\ReceiveInterface;
use Swoole\Server;

class ReceiveListener implements ReceiveInterface
{
    public function onReceive(Server $server, int $fd, int $reactorId, string $data): void
    {
        var_dump(" tcp receive");
        // TODO: Implement onReceive() method.
    }

}