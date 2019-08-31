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
use Stdlib\Helper\Sys;
use Swoole\Server as SwooleServer;

/**
 * Class WorkerStartListener
 * @package App\Event
 */
class WorkerStartListener implements WorkerStartInterface
{
    const INIT_LOGIC = 1;

    /**
     * @param SwooleServer $server
     * @param int $workerId
     */
    public function onWorkerStart(SwooleServer $server, int $workerId): void
    {
        Sys::setProcessTitle(sprintf('php-%s worker process (%s)', env("APP_NAME"),ROOT));
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