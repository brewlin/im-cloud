<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/24
 * Time: 20:26
 */

namespace App\cloud\app\Event;


use Swoole\HTTP\Request;
use Core\Swoole\OpenInterface;
use Swoole\Websocket\Server;

class OpenListener implements OpenInterface
{
    /**
     * @param Server $server
     * @param Request $request
     * token check '{"mid":123, "room_id":"live://1000", "platform":"web", "accepts":[1000,1001,1002]}'
     */
    public function onOpen(Server $server, Request $request): void
    {
        

    }

}