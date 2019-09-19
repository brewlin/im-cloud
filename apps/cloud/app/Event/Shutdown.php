<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/25 0025
 * Time: 下午 5:24
 */

namespace App\Event;


use Core\Console\Console;
use Log\Helper\Log;

/**
 * Class ShutdownListener
 * @package App\Event
 */
class Shutdown
{
    /**
     */
    public function shutdown(): void
    {
        Log::info("注销 注册中心 im-cloud-node 节点");
        Console::writeln(sprintf('<success>注销 注册中心 im-cloud-node 节点 now!</success>'));
        //注销节点
        $discovery = config("discovery");
        provider()->select()->deregisterService($discovery['consul']["register"]['Name']);
        //关闭连接

    }

}