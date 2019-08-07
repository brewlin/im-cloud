<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/2
 * Time: 19:43
 */

namespace App\Service\Service;
use App\Lib\LogicClient;
use App\Packet\Packet;
use App\Packet\Protocol;
use App\Packet\Task;
use App\Service\Dao\Bucket;
use Core\Cloud;
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use Grpc\Client\GrpcLogicClient;
use Im\Cloud\Operation;
use Im\Logic\HeartbeatReq;
use Log\Helper\Log;

/**
 * Class Heartbeat
 * @package App\Service\Service
 * @Bean()
 */
class Heartbeat
{
    const HeartBeatReply = '{"hreart":"ok"}';
    /**
     * heartbeat step 1
     * @return void
     */
    public function heartbeat():void
    {
        $fd = Context::value("fd");
        //grpc to logic node expire currrent time
        $server = LogicClient::getLogicClient();
        if(empty($server))
            throw  new \Exception("not find any logic node");
        \bean(Task::class)->deliver(Heartbeat::class,"heartbeatLogic",[$server,$fd]);
        /** @var Packet $packet */
        $packet = \bean(Packet::class);
        //pack data repy cliend
        $packet->setOperation(Operation::OpHeartbeatReply);
        $buf = $packet->pack(self::HeartBeatReply);
        Cloud::server()->getSwooleServer()->push($fd,$buf,WEBSOCKET_OPCODE_BINARY);

    }

    /**
     * step 2
     * @param int $fd
     */
    public static function heartbeatLogic(string $grpcServer ,int $fd)
    {
        $key = Bucket::key($fd);
        $mid = Bucket::mid($fd);
        if(empty($key)||empty($mid)){
            Log::error("fd:%d  is not exist key:%s mid:%s",$fd,$key,$mid);
            return;
        }
        $heartBeatReq = new HeartbeatReq();
        $host = env("APP_HOST","127.0.0.1").":".env("GRPC_PORT",9500);
        $heartBeatReq->setServer($host);
        $heartBeatReq->setKey($key);
        $heartBeatReq->setMid($mid);
        GrpcLogicClient::Heartbeat($grpcServer,$heartBeatReq);

    }

}