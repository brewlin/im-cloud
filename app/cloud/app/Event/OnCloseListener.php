<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/9 0009
 * Time: 下午 2:22
 */

namespace App\Event;

use App\Packet\Task;
use App\Service\Dao\Bucket;
use Core\Cloud;
use Core\Swoole\CloseInterface;
use Swoole\Server;

/**
 * Class OnCloseListener
 * @package App\cloud\app\Event
 */
class OnCloseListener implements CloseInterface
{
    public function onClose(Server $server, int $fd, int $reactorId): void
    {
        $clientInfo = Cloud::server()->getSwooleServer()->getClientInfo($fd);
        //表示为websocket连接
        if(isset($clientInfo['websocket_status']) && $clientInfo['websocket_status'] == WEBSOCKET_STATUS_ACTIVE){
            /** Task */
            bean(Task::class)->deliver(Bucket::class,"disconnect",[$fd]);
            return;
        }
        /**
         * 表示为tcp连接
         */
        if($clientInfo['server_port'] == env("TCP_PORT")){
            bean(Task::class)->deliver(Bucket::class,"disconnect",[$fd]);
            return;
        }

        // TODO: Implement onClose() method.
    }

}