<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/11
 * Time: 22:45
 */

namespace App\Event;


use App\Lib\Logic;
use App\Process\InitLogicProcess;
use Core\Co;
use Core\Swoole\WorkerStartInterface;
use ImQueue\Pool\PoolFactory;
use Log\Helper\CLog;
use Swoole\Coroutine;
use Swoole\Server as SwooleServer;

class WorkerStartListener implements WorkerStartInterface
{
    const INIT_LOGIC = 1;

    public function onWorkerStart(SwooleServer $server, int $workerId): void
    {
        $workerNum = env("WORKER_NUM",4);

//        if($workerId == self::INIT_LOGIC){
//            bean(InitLogicProcess::class)->run();
//        }
//        $scheduler = new Coroutine\Scheduler;
//        $scheduler->add(function(){
            Co::create(function (){
                Logic::loadOnline();
            },false);

//        });

    }

}