<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/9 0009
 * Time: 下午 2:52
 */

namespace App\cloud\app\Service\Service;

use Core\Container\Mapping\Bean;
use Grpc\Client\GrpcLogicClient;
use Im\Logic\DisconnectReq;
use App\Lib\LogicClient;
use Log\Helper\Log;

/**
 * Class Close
 * @package App\cloud\app\Service\Service
 * @Bean()
 */
class Disconnect
{
    /**
     * @param int $mid
     * @param string $key
     */
    public function disconnect(int $mid,string $key)
    {
        Log::debug("key:%s  disconnect",$key);
        if(empty($key) || empty($mid))return;

        $disReq = new DisconnectReq();
        $disReq->setMid($mid);
        $disReq->setKey($key);
        $serverId = env("APP_HOST","127.0.0.1").":".env("GRPC_PORT",9500);
        $disReq->setServer($serverId);
        $server = LogicClient::getLogicClient();
        /**
         * 请求logic节点注销相关信息
         */
        GrpcLogicClient::Disconnect($server,$disReq);


    }

}