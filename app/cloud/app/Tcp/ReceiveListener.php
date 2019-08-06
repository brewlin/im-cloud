<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/26 0026
 * Time: 下午 5:58
 */

namespace App\Tcp;


use App\Lib\LogicClient;
use App\Service\Dao\Bucket;
use App\Websocket\Exception\RequireArgException;
use Core\Swoole\ReceiveInterface;
use Im\Logic\ConnectReply;
use Im\Logic\ConnectReq;
use Log\Helper\Log;
use Swoole\Server;

class ReceiveListener implements ReceiveInterface
{
    /**
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     * @param string $data
     */
    public function onReceive(Server $server, int $fd, int $reactorId, string $data): void
    {
        $registerBucket = false;
        try
        {
            $data = json_decode($data, true);
            if (!$data)
                throw new \Exception("require token",0);

            //step 1
            $this->checkAuth($data);

            //step 2
            [$mid,$key,$roomId,$accepts,$heartbeat] = $this->registerLogic($data);

            Bucket::put($roomId,$key,$fd);
            $registerBucket = true;

        }catch (\Throwable $e)
        {
            $file = $e->getFile();
            $line = $e->getLine();
            $code = $e->getCode();
            $exception = $e->getMessage();
            $msg = $e->getMessage();
            $returnData = ['code' => $code,'msg' => $msg];
            Log::error("file:".$file." line:$line code:$code msg:$exception");
            if($registerBucket)
                Bucket::del($roomId,$key,$fd);
            $server->send($fd,json_encode($returnData));
            $server->close($fd);
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
        if(!$rpy){
            throw new \Exception("grpc to logic failed",0);
        }
        return [$rpy->getMid(),$rpy->getKey(),$rpy->getRoomID(),$rpy->getAccepts(),$rpy->getRoomID(),$rpy->getHeartbeat()];

    }

}