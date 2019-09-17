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
    public function push(int $operation,$body)
    {
        $proto = new Proto();
        $proto->setVer(1);
        $proto->setOp($operation);
        $proto->setBody($body);

        $broadcastReq = new BroadcastReq();
        $broadcastReq->setProtoOp($operation);
        $broadcastReq->setProto($proto);
        //get all instance
        $serviceList = CloudClient::getAllInstance();
        foreach ($serviceList as $server) {
            Log::info("brocast servicd:$server");
            GrpcCloudClient::Broadcast($server,$broadcastReq);
        }
    }

}