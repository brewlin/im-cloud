<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/15 0025
 * Time: 下午 5:24
 */

namespace App;


use Core\Console\Console;
use Log\Helper\Log;

/**
 * Class ShutdownListener
 * @package App\Event
 */
class Shutdown
{
    /**
     * @param
     */
    public function shutdown(): void
    {
        Log::info("注销 注册中心 im-logic-node 节点");
        Console::writeln(sprintf('<success>注销 注册中心 logic-s 节点 now!</success>'));
        //注销节点
        provider()->select()->deregisterService("grpc-im-logic-node");

    }

}