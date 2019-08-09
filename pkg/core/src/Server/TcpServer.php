<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/16
 * Time: 11:30
 */

namespace Core\Server;


use Core\Listener\ReceiveListener;
use Core\Swoole\SwooleEvent;

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
        // TODO: Implement getSetting() method.
    }

    /**
     * @return array
     */
    public function getOn(): array
    {
        return [
                SwooleEvent::RECEIVE => new ReceiveListener()
            ];
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