<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/11/29 0029
 * Time: 下午 3:24
 */

namespace App\Service\Service;

use App\Lib\LogicClient;
use App\Packet\Packet;
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use Grpc\Client\GrpcLogicClient;
use Im\Logic\PushAllReq;
use Im\Logic\PushKeysReq;
use Im\Logic\PushMidsReq;
use Im\Logic\PushRoomReq;
use Log\Helper\Log;

/**
 * Class Push
 * @package App\Service\Service
 * @Bean()
 */
class Push
{
    const Action = "cmd";
    const Body = "data";

    const Exector = ["pushKeys","pushMids","pushRoom","pushAll"];
    /**
     * 推送数据到logic节点处理
     */
    public function push()
    {
        /** @var array  $data */
        $packet = Context::value(Packet::class);
        //数据包格式错误
        if(!isset($packet[self::Action]) || !in_array($packet[self::Action],self::Exector) || !is_array($packet) || !isset($packet[self::Body])){
            Log::error(sprintf("parse client data error,client fd:%s,data:%s",Context::value(),is_array($packet)?json_encode($packet):$packet));
        }
        $this->{$packet[self::Action]}($packet[self::Body]);
    }

    /**
     * 根据keys 进行推送
     * @param $data
     */
    public function pushKeys($data)
    {
        if(!isset($data['keys']) || empty($data['keys']) || empty($data['msg']))
        {
            Log::error("pushkeys require args o:".json_encode($data));
            return;
        }
        list($keys,$msg) = [$data['keys'],$data["msg"]];
        $pushkeyReq = new PushKeysReq();
        $pushkeyReq->setKeys(is_array($keys)?$keys:[$keys])
                   ->setMsg($msg);
        $nodes = LogicClient::getLogicClient();
        //推送到logic节点
        GrpcLogicClient::PushKeys($nodes,$pushkeyReq);
    }

    /**
     * 根据mids 进行推送
     * @param $data
     */
    public function pushMids($data)
    {
        if(!isset($data['mids']) || empty($data['mids']) || empty($data['msg']))
        {
            Log::error("pushmids require args o:".json_encode($data));
            return;
        }
        list($mids,$msg) = [$data['mids'],$data["msg"]];
        $pushMids = new PushMidsReq();
        $pushMids->setMids(is_array($mids)?$mids:[$mids])
                 ->setMsg($msg);
        $nodes = LogicClient::getLogicClient();
        //推送到logic节点
        GrpcLogicClient::PushMids($nodes,$pushMids);
    }

    /**
     * 房间广播
     * @param $data
     */
    public  function pushRoom($data)
    {
        if(!isset($data['type']) || !isset($data["room"]) || empty($data['type']) || empty($data["room"])|| empty($data['msg']))
        {
            Log::error("pushrooms require args o:".json_encode($data));
            return;
        }
        list($type,$room,$msg) = [$data["type"],$data["room"],$data["msg"]];
        $pushRoomReq = new PushRoomReq();
        $pushRoomReq->setType($type)
                    ->setRoom($room)
                    ->setMsg($msg);
        $nodes = LogicClient::getLogicClient();
        //推送到logic节点
        GrpcLogicClient::PushRoom($nodes,$pushRoomReq);

    }

    /**
     * 世界广播
     * @param $data
     */
    public function pushAll($data)
    {
        if(empty($data['msg']))
        {
            Log::error("pushall require args o:".json_encode($data));
            return;
        }
        $msg = $data["msg"];
        $pushAllReq = new PushAllReq();
        $pushAllReq->setMsg($msg);
        $nodes = LogicClient::getLogicClient();
        //推送到logic节点
        GrpcLogicClient::PushAll($nodes,$pushAllReq);
    }

}