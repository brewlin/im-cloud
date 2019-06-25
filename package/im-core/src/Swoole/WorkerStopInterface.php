<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/25 0025
 * Time: 下午 4:39
 */

namespace Core\Swoole;
use Swoole\Server as SwooleServer;

interface WorkerStopInterface
{
    /**
     * @param SwooleServer $server
     * @param int $workerId
     */
    public function onWorkerStop(SwooleServer $server, int $workerId): void;

}