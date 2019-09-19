<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/12 0012
 * Time: 下午 2:56
 */

namespace App;

use Core\App;
use Core\Contract\ApplicationInterface;

/**
 * Class Application
 * @package App
 */
class Application extends App implements ApplicationInterface
{
    /**
     * coroutine server processor
     * @var ApplicationInterface[]
     */
    protected $processor = [
        TcpServer::class,
        WebsocketServer::class
    ];

    /**
     * start the tcp and websocket server
     */
    public function handle(): void
    {
        foreach ($this->processor as $processor)
        {
            /** @var ApplicationInterface $server */
            $server = new $processor();
            go(function()use($server) {
                $server->handle();
            });
        }
    }
}