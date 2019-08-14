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
    /**
     * 监听worker进程  task进程 退出事件
     *
     * @param SwooleServer $server
     * @param integer $workerId
     * @return void
     */
    public function onWorkerStop(SwooleServer $server, int $workerId): void
    {
        Log::info("workerid:".$workerId." is stop");
    }

}