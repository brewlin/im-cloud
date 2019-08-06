<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/27
 * Time: 10:11
 */

namespace App\Service\Dao;


use App\Packet\Packet;
use App\Packet\Protocol;
use Core\Cloud;
use Core\Container\Mapping\Bean;
use Log\Helper\Log;

/**
 * Class Push
 * @package App\Lib
 * @Bean()
 */
class Push
{
    /**
     * @param int $fd
     * @param $data
     */
    public function push(string $key,$data)
    {
        Log::info("Cloud push:{$key}  data:".json_encode($data));
        if(!($fd = Bucket::fd($key))) return;
        if(!($clientinfo = Cloud::server()->getSwooleServer()->getClientInfo($fd))){
            Log::info("连接 fd:{$fd} 不存在,待发送数据:".json_encode($data));
           return;
        }
        //判断为websocket连接且已经握手完毕
        if(isset($clientinfo['websocket_status']) && $clientinfo['websocket_status'] == WEBSOCKET_STATUS_FRAME){
            /** @var Packet $packet */
            $packet = \bean(Packet::class);
            $packet->setOperation(Protocol::PushClient);
            $buf = $packet->pack($data);
            Cloud::server()->getSwooleServer()->push($fd,$buf,WEBSOCKET_OPCODE_BINARY);
            return;
        }
        Cloud::server()->getSwooleServer()->send($fd,$data);
    }

}