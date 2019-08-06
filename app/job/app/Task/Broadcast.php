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
use Im\Cloud\BroadcastReq;
use Im\Cloud\Proto;
use Log\Helper\Log;

/**
 * Class Broadcast
 * @package App\Lib
 * @Bean()
 */
class Broadcast
{
    /**
     * @param int $operation
     * @param $body
     * @param int $speed
     */
    public function push(array $serviceList,int $operation,$body,int $speed)
    {
        $proto = new Proto();
        $proto->setVer(1);
        $proto->setOp($operation);
        $proto->setBody($body);

        $broadcastReq = new BroadcastReq();
        $broadcastReq->setSpeed($speed);
        $broadcastReq->setProtoOp($operation);
        $broadcastReq->setProto($proto);
        foreach ($serviceList as $server) {
            Log::info("brocast servicd:$server");
            GrpcCloudClient::Broadcast($server,$broadcastReq);
        }
    }

}