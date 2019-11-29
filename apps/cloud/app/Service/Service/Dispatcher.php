<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/2 0002
 * Time: 下坈 6:08
 */

namespace App\Service\Service;

use App\Packet\Packet;
use App\Packet\Protocol;
use App\Service\Service\Auth;
use App\Service\Service\Heartbeat;
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use Im\Cloud\Operation;

/**
 * Class Dispatch
 * @package App\Websocket
 * @Bean()
 */
class Dispatcher
{
    /**
     * @param Packet $packet
     * @param int $fd
     */
    public function dispatch()
    {
        /** @var Packet $packet */
        $packet = Context::value(Packet::class);
        switch ($packet->getOperation())
        {
            //register
            case Operation::OpAuth:
                container()->get(Auth::class)
                           ->auth();
                break;
            //heartbeat
            case Operation::OpHeartbeat:
                container()->get(Heartbeat::class)
                           ->heartbeat();
                break;
            //grpc push
            case Operation::OpRaw:
                \bean(Push::class)->push();
                break;
            default:
        }
    }


}