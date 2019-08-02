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
use Core\Container\Mapping\Bean;
use Core\Context\Context;

/**
 * Class Dispatch
 * @package App\Websocket
 * @Bean()
 */
class Dispatcher
{
    public function dispatch(Packet $packet,int $fd)
    {
        switch ($packet->getOperation())
        {
            case Protocol::Auth:
                container()->get(Auth::class)
                           ->auth($packet->getBody());
                break;
            case Protocol::Heartbeat:
        }
    }


}