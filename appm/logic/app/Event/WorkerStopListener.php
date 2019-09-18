<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/25 0025
 * Time: 下午 4:38
 */

namespace App\Event;


use Core\Swoole\WorkerStopInterface;
use Log\Helper\Log;
use Swoole\Coroutine;
use Swoole\Server as SwooleServer;

class WorkerStopListener implements WorkerStopInterface
{
    public function onWorkerStop(SwooleServer $server, int $workerId): void
    {
        Log::info("workerid:".$workerId." is stop");
    }

}