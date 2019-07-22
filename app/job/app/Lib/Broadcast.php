<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/22 0022
 * Time: 下午 2:49
 */

namespace App\Lib;

use Core\Container\Mapping\Bean;
use Im\Cloud\BroadcastReq;
use Im\Cloud\Proto;
use Log\Helper\CLog;

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
    public function push(int $operation,$body,int $speed)
    {
        $proto = new Proto();
        $proto->setVer(1);
        $proto->setOp($operation);
        $proto->setBody($body);

        $broadcastReq = new BroadcastReq();
        $broadcastReq->setSpeed($speed);
        $broadcastReq->setProtoOp($operation);
        $broadcastReq->setProto($proto);

        foreach (CloudClient::$serviceList as $server) {
            $client = CloudClient::getCloudClient($server);
            CLog::info("brocast servicd:$server");
            $client->Broadcast($broadcastReq);
        }
    }

}