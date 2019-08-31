<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/11
 * Time: 22:45
 */

namespace App\Event;


use App\Packet\Task;
use App\Process\TaskProcess;
use Co\Scheduler;
use Core\App;
use Core\Swoole\WorkerStartInterface;
use Log\Helper\Log;
use Process\ProcessManager;
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
     * 监听worker进程  task进程 启动事件
     *
     * @param SwooleServer $server
     * @param integer $workerId
     * @return void
     */
    public function onWorkerStart(SwooleServer $server, int $workerId): void
    {
        Sys::setProcessTitle(sprintf('php-%s worker process (%s)', env("APP_NAME"),ROOT));
        //接受自定义进程taskProcess 投递过来的请求
        if(App::isWorkerStatus()){
            //创建协程周期执行任务
//            $scheduler = new Scheduler();
//            $scheduler->add(function (){
//                事件循环
//                while(true){
//                    $buf  = ProcessManager::getProcesses(TaskProcess::Name)->read();
//                    try{
//                        /** @var Task $task */
//                        $task = bean(Task::class)->unpack();
//                        bean($task->getClass())->{$task->getMethod()}(...$task->getArg());
//                    }catch(\Throwable $e)
//                    {
//                        Log::error("read taskprocess 进程 数据解析失败 buf:%s msg:%s",$buf,$e->getMessage());
//                    }
//                }
//
//            });
//            $scheduler->start();
        }
    }

}