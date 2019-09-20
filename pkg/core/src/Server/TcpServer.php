<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/16
 * Time: 11:30
 */

namespace Core\Server;


use Core\Container\Mapping\Bean;
use Core\Listener\ReceiveListener;
use Core\Swoole\SwooleEvent;

/**
 * Class TcpServer
 * @package Core\Server
 * @Bean()
 */
class TcpServer implements ServerInterface
{

    /**
     * Start swoole server
     *
     * @return void
     */
    public function start(): void
    {
        // TODO: Implement start() method.
    }

    /**
     * Stop server
     *
     * @return bool
     */
    public function stop(): bool
    {
        // TODO: Implement stop() method.
    }

    /**
     * Restart server
     */
    public function restart(): void
    {
        // TODO: Implement restart() method.
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return env("TCP_HOST","127.0.0.1");
        // TODO: Implement getHost() method.
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return env("TCP_PORT",9502);
        // TODO: Implement getPort() method.
    }

    /**
     * @return int
     */
    public function getMode(): int
    {
        // TODO: Implement getMode() method.
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return SWOOLE_SOCK_TCP;
        // TODO: Implement getType() method.
    }

    /**
     * @return array
     */
    public function getSetting(): array
    {
        $set = config("server");
        if(empty($set))return [
            'open_eof_check'     => false,
            'package_max_length' => 2048,
        ];

        return $set["tcp"];
        // TODO: Implement getSetting() method.
    }

    /**
     * @return array
     */
    public function getOn(): array
    {
        $event = config("event");
        $listener = [SwooleEvent::RECEIVE,SwooleEvent::CLOSE];
        $on = [];
        foreach ($event as $e => $f){
            if(in_array($e,$listener)){
                $on[$e] = $f;
            }
        }
        return array_merge([
                SwooleEvent::RECEIVE => new ReceiveListener()
            ],$on);
        // TODO: Implement getOn() method.
    }

    /**
     * @return array
     */
    public function getListener(): array
    {
        // TODO: Implement getListener() method.
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        // TODO: Implement getTypeName() method.
        return "Tcp";
    }
}