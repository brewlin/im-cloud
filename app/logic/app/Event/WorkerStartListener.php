<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/11
 * Time: 22:45
 */

namespace App\Event;


use App\Lib\Logic;
use Core\Co;
use Core\Swoole\WorkerStartInterface;
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
//            Co::create(function (){
//                Logic::loadOnline();
//            },false);

//        });

    }

}