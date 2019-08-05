<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/22 0022
 * Time: 下午 2:49
 */

namespace App\Task;

use App\Lib\CloudClient;
use Core\Container\Mapping\Bean;
use Grpc\Client\GrpcCloudClient;
use Im\Cloud\BroadcastRoomReq;
use Im\Cloud\Proto;
use Log\Helper\CLog;

/**
 * Class PushRoom
 * @package App\Lib
 * @Bean()
 */
class PushRoom
{
    /**
     * 暂时没有做合并优化
     * @param string $room
     * @param int $operation
     * @param $msg
     */
    public function push(array $serviceList,string $room,int $operation,$msg)
    {
        $proto = new Proto();
        $proto->setVer(1);
        $proto->setBody($msg);
        $proto->setOp($operation);

        $arg = new BroadcastRoomReq();
        $arg->setRoomID($room);
        $arg->setProto($proto);
        foreach ($serviceList as $server) {
            CLog::info("brocatroom roomid:$room servicd:$server");
            GrpcCloudClient::BroadcastRoom($server,$arg);
        }

    }

}