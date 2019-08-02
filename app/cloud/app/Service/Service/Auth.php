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
use App\Service\Dao\Bucket;
use App\Websocket\Exception\RequireArgException;
use Core\Cloud;
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use Im\Logic\ConnectReply;
use Im\Logic\ConnectReq;
use Log\Helper\CLog;

/**
 * Class Auth
 * @package App\Service\Service
 * @Bean()
 */
class Auth
{
    /**
     * connect event
     * @param array $body
     * @param int $fd
     * @throws \Exception
     */
    public function auth(array $body)
    {
        $fd = Context::value("fd");
        //check data
        $this->checkAuth($body);
        //grpc - register to logic node
        try{
            //step 1
            [$mid,$key,$roomId,$accepts,$heartbeat] = $this->registerLogic($body);
            //step 2
            Bucket::put($roomId,$key,$fd);
            //step 3
            $this->registerSuccess();
        }catch (\Throwable $e){
            CLog::error("auth error fd:$fd {$e->getMessage()}");
            Bucket::del($roomId,$key,$fd);
        }
    }
    /**
     * step 1
     * token check '{"mid":123, "room_id":"live://1000", "platform":"web", "accepts":[1000,1001,1002]}'
     * @param array $data
     * @throws \Exception
     */
    public function checkAuth(array $data)
    {
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
        $rpcClient = LogicClient::getLogicClient();
        $connectReq = new ConnectReq();

        $serverId = env("APP_HOST","127.0.0.1").":".env("GRPC_PORT",9500);
        $connectReq->setServer($serverId);
        $connectReq->setCookie("");
        $connectReq->setToken(json_encode($data));
        /** @var ConnectReply $rpy */
        $rpy = $rpcClient->Connect($connectReq)[0];
        if(!is_object($rpy))
            throw new \Exception("grpc to logic failed");
        if(!$rpy){
            throw new \Exception("grpc to logic failed",0);
        }
        return [$rpy->getMid(),$rpy->getKey(),$rpy->getRoomID(),$rpy->getAccepts(),$rpy->getHeartbeat()];

    }

    /**
     * step 3
     */
    public function registerSuccess()
    {
        $fd = Context::value("fd");
        /** @var Packet $packet */
        $packet = \bean(Packet::class);
        $packet->setOperation(Protocol::AuthReplyOk);
        $buf = $packet->pack(json_encode(["ok" => "yes"]));
        Cloud::server()->getSwooleServer()->push($fd,$buf);
        CLog::info("register success reply client buf:".$buf);
    }

}