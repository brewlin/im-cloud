<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/25 0025
 * Time: 下午 5:22
 */

namespace Core\Swoole;
use Swoole\Server as SwooleServer;

interface ShutdownInterface
{

    /**
     * Shutdown event
     * @param SwooleServer $server
     */
    public function onShutdown(SwooleServer $server): void;

}