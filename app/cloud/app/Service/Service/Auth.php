<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/2 0002
 * Time: 下午 6:14
 */

namespace App\Service\Service;

use App\Lib\LogicClient;
use App\Packet\Packet;
use App\Packet\Protocol;
use App\Packet\Task;
use App\Service\Dao\Bucket;
use App\Websocket\Exception\RequireArgException;
use Core\Cloud;
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use Grpc\Client\GrpcLogicClient;
use Im\Logic\ConnectReply;
use Im\Logic\ConnectReq;
use Log\Helper\Log;

/**
 * Class Auth
 * @package App\Service\Service
 * @Bean()
 */
class Auth
{
    const MisBody = "require data";
    /**
     * connect event
     * @throws \Exception
     */
    public function auth()
    {
        $fd = Context::value("fd");
        /** @var Packet $packet */
        $packet = Context::value(Packet::class);
        $body = $packet->getBody();
        //check data
        $this->checkAuth($packet);
        //grpc - register to logic node
        try{
            //step 1
            [$mid,$key,$roomId,$accepts,$heartbeat] = $this->registerLogic($body);
            //step 2
            /** @var Task $task */
            \bean(Task::class)->deliver(Bucket::class,"put",[$key,$fd,$mid,$roomId]);
            //step 3
            $this->registerSuccess();
        }catch (\Throwable $e){
            Log::error("auth error fd:$fd {$e->getMessage()}");
            /** @var Task $task */
            $task = \bean(Task::class);
            $task->setClass(Bucket::class);
            //del fd
            $task->setMethod("del");
            $task->setArg([$key,$fd,$roomId]);
            $task->exec();
        }
    }
    /**
     * step 1
     * token check '{"mid":123, "room_id":"live://1000", "platform":"web", "accepts":[1000,1001,1002]}'
     * @param array $data
     * @throws \Exception
     */
    public function checkAuth(Packet $packet)
    {
        $data = $packet->getBody();
        if (empty($data))
            throw new \Exception(self::MisBody,0);
        $keyField = ['mid','room_id','platform','accepts'];
        foreach ($keyField as $key){
            if(!isset($data[$key])){
                throw new RequireArgException("rquire arg $key",0);
            }
        }

    }
    /**
     * step 2
     * token check '{"mid":123, "room_id":"live://1000", "platform":"web", "accepts":[1000,1001,1002]}'
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function registerLogic(array $data)
    {
        $server = LogicClient::getLogicClient();
        if(empty($server))
            throw  new \Exception("not find any logic node");
        $connectReq = new ConnectReq();
        /** @var \Im\Logic\LogicClient $rpcClient */
        $rpcClient  = null;
        $serverId = env("APP_HOST","127.0.0.1").":".env("GRPC_PORT",9500);
        $connectReq->setServer($serverId);
        $connectReq->setCookie("");
        $connectReq->setToken(json_encode($data));
        /** @var ConnectReply $rpy */
        $rpy = GrpcLogicClient::Connect($server,$connectReq)[0];
        if(!is_object($rpy))
            throw new \Exception("grpc to logic failed");
        if(!$rpy){
            throw new \Exception("grpc to logic failed",0);
        }
        return [$rpy->getMid(),$rpy->getKey(),$rpy->getRoomID(),$rpy->getAccepts(),$rpy->getHeartbeat()];

    }

    /**
     * websocket 针对前段js 需要设置websocket_opcode_binary 二进制流传输
     * step 3
     */
    public function registerSuccess()
    {
        $fd = Context::value("fd");
        /** @var Packet $packet */
        $packet = \bean(Packet::class);
        $packet->setOperation(Protocol::AuthReplyOk);
        $buf = $packet->pack(json_encode(["ok" => "yes"]));
        Cloud::server()->getSwooleServer()->push($fd,$buf,WEBSOCKET_OPCODE_BINARY);
        Log::info("register success reply client buf:".$buf);
    }

}