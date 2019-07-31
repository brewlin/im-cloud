<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/26
 * Time: 21:31
 */

namespace App\Websocket;


use App\Lib\LogicClient;
use App\Packet\Packet;
use App\Service\Dao\Bucket;
use App\Websocket\Exception\HandshakeException;
use App\Websocket\Exception\RequireArgException;
use Core\Swoole\MessageInterface;
use Im\Logic\ConnectReply;
use Im\Logic\ConnectReq;
use Log\Helper\CLog;
use function Swlib\Http\str;
use Swoole\Websocket\Frame;
use Swoole\Websocket\Server;

/**
 * websocket 接收事件入口
 * Class MessageListener
 * @package App\Event
 */
class MessageListener implements MessageInterface
{
    /**
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server, Frame $frame): void
    {
        $registerBucket = false;
        CLog::info("fd:{$frame->fd} data:{$frame->data}");
        try {
            $data = bean(Packet::class)->unpack($frame->data);
            $data = json_decode($data, 1);
            if (!$data)
                throw new \Exception("require token",0);

            //step 1
            $this->checkAuth($data);

            //step 2
            [$mid,$key,$roomId,$accepts,$heartbeat] = $this->registerLogic($data);

            Bucket::put($roomId,$key,$frame->fd);
            $registerBucket = true;
        } catch (\Throwable $e) {
            $file = $e->getFile();
            $line = $e->getLine();
            $code = $e->getCode();
            $exception = $e->getMessage();
            $msg = $e->getMessage();
            $returnData = ['code' => $code,'msg' => $msg];
            CLog::error("file:".$file." line:$line code:$code msg:$exception");
            if($registerBucket)
                Bucket::del($roomId,$key,$frame->fd);
            $server->push($frame->fd,json_encode($returnData));
            $server->close($frame->fd);
        }
    }

    /**
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
     * token check '{"mid":123, "room_id":"live://1000", "platform":"web", "accepts":[1000,1001,1002]}'
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function registerLogic(array $data)
    {
        $rpcClient = LogicClient::getLogicClient();
        $connectReq = new ConnectReq();

        $serverId = env("GRPC_HOST","127.0.0.1").":".env("GRPC_PORT",9500);
        $connectReq->setServer($serverId);
        $connectReq->setCookie("");
        $connectReq->setToken(json_encode($data));

        /** @var ConnectReply $rpy */
        $rpy = $rpcClient->Connect($connectReq);
        if(!is_object($rpy))
            throw new \Exception("grpc to logic failed");
        if(!$rpy){
            throw new \Exception("grpc to logic failed",0);
        }
        return [$rpy->getMid(),$rpy->getKey(),$rpy->getRoomID(),$rpy->getAccepts(),$rpy->getRoomID(),$rpy->getHeartbeat()];

    }

}