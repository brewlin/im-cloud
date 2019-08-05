<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/11
 * Time: 22:45
 */

namespace App\Event;


use App\Consumer\Consumer;
use Core\App;
use Core\Swoole\WorkerStartInterface;
use Swoole\Server as SwooleServer;

class WorkerStartListener implements WorkerStartInterface
{
    const INIT_LOGIC = 1;

    public function onWorkerStart(SwooleServer $server, int $workerId): void
    {
        if(App::isWorkerStatus()){
            //启动的n个 worker进程 分别作为消费者进程消费，每个进程会直接阻塞直到消费到数据
            consumer()->consume(new Consumer());
        }
    }

}