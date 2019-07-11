<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/11
 * Time: 22:45
 */

namespace App\logic\app\Event;


use App\Process\InitLogicProcess;
use Core\Swoole\WorkerStartInterface;
use Swoole\Server as SwooleServer;

class WorkerStartListener implements WorkerStartInterface
{
    const INIT_LOGIC = 1;
    public function onWorkerStart(SwooleServer $server, int $workerId): void
    {
        $workerNum = env("WORKER_NUM",4);

        //单独分配一个进程进行初始化处理，最后在分发到个个同级子进程中
        if($workerId == self::INIT_LOGIC){
            bean(InitLogicProcess::class)->run();
        }

    }

}