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
use Im\Cloud\Proto;
use Im\Cloud\PushMsgReq;
use Log\Helper\CLog;

/**
 * @Bean()
 * Class PushKey
 * @package App\Lib
 */
class PushKey
{
    /**
     * 进行grpc 和 cloud 节点通讯
     * @param int $operation
     * @param string $server
     * @param array $subkey
     * @param $body
     */
    public function push(array $serviceList ,int $operation ,string $server , array $subkey , $body)
    {
        $proto = new Proto();
        $proto->setOp($operation);
        $proto->setVer(1);
        $proto->setBody($body);

        $pushMsg = new PushMsgReq();
        $pushMsg->setKeys($subkey);
        $pushMsg->setProto($proto);
        $pushMsg->setProtoOp($operation);
        $serverId = $serviceList[$server];
        if(empty($serverId)){
            CLog::error("pushkey not exist grpc client server: $server ");
            return;
        }
        GrpcCloudClient::PushMsg($serverId,$pushMsg);
    }

}