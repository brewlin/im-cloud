<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/2 0002
 * Time: 下午 6:08
 */

namespace App\Websocket;

use App\Packet\Packet;
use App\Packet\Protocol;
use App\Service\Service\Auth;
use App\Service\Service\Heartbeat;
use Core\Container\Mapping\Bean;
use Core\Context\Context;

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
            case Protocol::Auth:
                container()->get(Auth::class)
                           ->auth();
                break;
            //heartbeat
            case Protocol::Heartbeat:
                container()->get(Heartbeat::class)
                           ->heartbeat();
                break;
            default:
        }
    }


}