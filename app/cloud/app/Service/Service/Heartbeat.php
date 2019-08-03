<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/2
 * Time: 19:43
 */

namespace App\Service\Service;
use App\Packet\Packet;
use App\Packet\Protocol;
use Core\Cloud;
use Core\Container\Mapping\Bean;
use Core\Context\Context;

/**
 * Class Heartbeat
 * @package App\Service\Service
 * @Bean()
 */
class Heartbeat
{
    const HeartBeatReply = '{"hreart":"ok"}';
    /**
     * heartbeat
     * @return void
     */
    public function heartbeat():void
    {
        $fd = Context::value("fd");
        /** @var Packet $packet */
        $packet = \bean(Packet::class);
        $packet->setOperation(Protocol::HeartbeatReplyOk);
        $buf = $packet->pack(self::HeartBeatReply);
        Cloud::server()->getSwooleServer()->push($fd,$buf,WEBSOCKET_OPCODE_BINARY);

    }

}