<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/27
 * Time: 10:11
 */

namespace App\Service\Dao;


use App\Packet\Packet;
use Core\Cloud;
use Core\Container\Mapping\Bean;
use Log\Helper\Log;

/**
 * Class Push
 * @package App\Lib
 * @Bean()
 */
class BroadcastRoom
{
    /**
     * @param int $fd
     * @param $data
     */
    public function push(string $roomId,int $op,$body)
    {
        Log::info("Cloud broadcast push roomId:{$roomId}  data:".json_encode($body));
        if(!($fds = Bucket::roomfds($roomId))){
            Log::info("roomId :%s is empty not user",$roomId);
            return;
        }
        foreach ($fds as $fd){
            if(!($clientinfo = Cloud::server()->getSwooleServer()->getClientInfo($fd))){
                Log::info("连接 fd:{$fd} 不存在,待发送数据:".json_encode($body));
                continue;
            }
            //判断为websocket连接且已经握手完毕
            if(isset($clientinfo['websocket_status']) && $clientinfo['websocket_status'] == WEBSOCKET_STATUS_FRAME){
                /** @var Packet $packet */
                $packet = \bean(Packet::class);
                $packet->setOperation($op);
                $buf = $packet->pack($body);
                Cloud::server()->getSwooleServer()->push($fd,$buf,WEBSOCKET_OPCODE_BINARY);
                continue;
            }
            Log::error("fd{$fd} 未连接");
        }
    }

}