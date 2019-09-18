<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/26 0026
 * Time: 下午 5:58
 */

namespace App\Tcp;


use App\Lib\LogicClient;
use App\Service\Dao\Bucket;
use App\Service\Service\Dispatcher;
use App\Websocket\Exception\RequireArgException;
use Core\Swoole\ReceiveInterface;
use Im\Logic\ConnectReply;
use Im\Logic\ConnectReq;
use Log\Helper\Log;
use App\Packet\Packet;
use Core\Context\Context;
use Swoole\Coroutine\Server;

/**
 * Class ReceiveListener
 * @package App\Tcp
 */
class ReceiveListener
{
    /**
     * @param Server $server
     * @param int $fd
     * @param string $data
     */
    public function onReceive(Server $server, int $fd, string $data): void
    {
        /** @var Packet $packet */
        $packet = bean(Packet::class)->unpack($data);
        Context::withValue(Packet::class, $packet);
        Context::withValue("fd", $fd);

        //dispatch
        container()->get(Dispatcher::class)
            ->dispatch();
    }

}