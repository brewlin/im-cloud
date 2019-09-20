<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/13 0013
 * Time: 下午 6:25
 */

namespace Core;


use Core\Server\Server;

class Cloud
{
    /**
     * @var App
     */
    public static $app;

    /**
     * get app server 
     * @return App
     */
    public static  function app():App{
        return self::$app;
    }

    /**
     * get swoole server
     * @return Server
     */
    public static function server():Server
    {
        return Server::getServer();
    }
    /**
     * @return \Swoole\Server
     */
    public static function swooleServer():\Swoole\Server
    {
        return self::server()->getSwooleServer();

    }

}