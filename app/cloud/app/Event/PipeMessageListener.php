<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/25 0025
 * Time: 下午 2:27
 */

namespace App\Event;


use App\Lib\LogicClient;
use Core\Swoole\PipeMessageInterface;
use Swoole\Server;

class PipeMessageListener implements PipeMessageInterface
{
    /**
     * 主要用来同步所有可用的服务节点，自定义子进程从consul中心拉取下来
     * 在同步到个个worker进程中
     * @param Server $server
     * @param int $srcWorkerId
     * @param mixed $message
     */
    public function onPipeMessage(Server $server, int $srcWorkerId, $message): void
    {
        //将grpc调用的可用服务更新
//        LogicClient::$serviceList = $message;
        LogicClient::updateService($message);
    }

}