<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/27
 * Time: 10:11
 */

namespace App\Service\Dao;


use App\Connection\Connection;
use App\Packet\Packet;
use App\Packet\Protocol;
use Core\Cloud;
use Core\Container\Mapping\Bean;
use Log\Helper\Log;
use \App\Connection\Bucket;

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
    public function push(string $key,int $op,$body)
    {
        Log::info("Cloud push:{$key}  data:".json_encode($body));
        if(!($fd = (\bean(Bucket::class)->fd($key))))return;

        self::pushFd($fd,$op,$body);
    }

    /**
     * @param $fd
     * @param $body
     */
    public function pushFd($fd,$op,$body)
    {
        /** @var Connection $conn */
        $conn = \bean(\App\Connection\Bucket::class)->get($fd);
        if(!$conn){
            Log::info("连接 fd:{$fd} 不存在,待发送数据:".json_encode($body));
            return;
        }

        //判断为websocket连接且已经握手完毕
        if($conn->isWs())
        {
            /** @var Packet $packet */
            $packet = \bean(Packet::class);
            $packet->setOperation($op);
            $buf = $packet->pack($body);
            $conn->push($buf,WEBSOCKET_OPCODE_BINARY);
            return;
        //TCP send 推送
        }else if($conn->isTcp())
        {
            $packet = \bean(Packet::class);
            $packet->setOperation($op);
            $buf = $packet->pack($body);
            $conn->send($buf);
            return;
        }
        Log::error("fd{$fd} 未连接");
    }

}