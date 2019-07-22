<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/25 0025
 * Time: 下午 5:24
 */

namespace App\Event;


use Co\Context;
use Core\Co;
use Core\Context\ContextWaitGroup;
use Core\Swoole\ShutdownInterface;
use Swoft\Log\Helper\CLog;
use Swoole\Coroutine;
use Swoole\Server as SwooleServer;

class ShutdownListener implements ShutdownInterface
{
    public function onShutdown(SwooleServer $server): void
    {
        CLog::info("注销 注册中心 im-cloud-node 节点");
        //注销节点
//        Co::create(function (){
            provider()->select()->deregisterService("im-cloud-node");
//        });
//        sleep(10);

    }

}