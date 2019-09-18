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
use Log\Helper\Log;
use Swoole\Coroutine;
use Swoole\Server as SwooleServer;

/**
 * Class ShutdownListener
 * @package App\Event
 */
class ShutdownListener implements ShutdownInterface
{
    /**
     * @param SwooleServer $server
     */
    public function onShutdown(SwooleServer $server): void
    {
//        Log::info("注销 注册中心 im-job-node 节点");
        //注销节点
//        Co::create(function (){
//            provider()->select()->deregisterService("im-cloud-node");
//        });
//        sleep(10);

    }

}