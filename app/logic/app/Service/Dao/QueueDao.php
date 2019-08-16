<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/5 0005
 * Time: 下午 4:01
 */

namespace App\Service\Dao;


use App\Lib\Producer;
use Core\Container\Mapping\Bean;
use Im\Cloud\Proto;
use Im\Logic\PushMsg;
use Log\Helper\Log;

/**
 * Class QueueDao
 * @package App\Service\Dao
 * @Bean()
 */
class QueueDao
{
    /**
     * pushMsg
     * @param int $op
     * @param string $server
     * @param array $keys
     * @param $msg
     * @throws \Throwable
     */
    public function pushMsg(int $operation,string $server,array $keys, $msg)
    {
        $type = PushMsg\Type::PUSH;
        $pushmsg = compact("type","operation","server","keys","msg");
        Log::info("push msg to job node data:".json_encode($pushmsg));
        /** @var Producer $producers */
        $producers = \bean(Producer::class);
        //发送到队列里
        producer()->produce($producers->producer($pushmsg));
    }

    /**
     * broadcastRoommsg
     * @param int $op
     * @param string $room
     * @param $msg
     */
    public function broadcastRoomMsg(int $operation,string $room,$msg)
    {

        $type = PushMsg\Type::ROOM;
        $pushmsg = compact("type","operation","room","msg");
        Log::info("push msg to job node data:".json_encode($pushmsg));
        /** @var Producer $producers */
        $producers = \bean(Producer::class);

        //发送到队列里
        producer()->produce($producers->producer($pushmsg));
    }

    /**
     * broadcast
     * @param int $op
     * @param int $peed
     * @param $msg
     */
    public function broadcastMsg(int $operation,$msg)
    {
        $type = PushMsg\Type::BROADCAST;
        $pushmsg = compact("type","operation","msg");
        Log::info("push msg to job node data:".json_encode($pushmsg));

        /** @var Producer $producers */
        $producers = \bean(Producer::class);

        //发送到队列里
        producer()->produce($producers->producer($pushmsg));
    }

}