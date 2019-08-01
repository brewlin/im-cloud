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
        $pushmsg = new PushMsg();
        $pushmsg->setType(PushMsg\Type::PUSH);
        $pushmsg->setOperation($operation);
        $pushmsg->setServer($server);
        $pushmsg->setKeys($keys);
        $pushmsg->setMsg($msg);
        //发送到队列里
        producer()->produce(new Producer(compact("operation","server","keys","msg")));
    }

    /**
     * broadcastRoommsg
     * @param int $op
     * @param string $room
     * @param $msg
     */
    public function broadcastRoomMsg(int $op,string $room,$msg)
    {
        $pushmsg = new PushMsg();
        $pushmsg->setType(PushMsg\Type::ROOM);
        $pushmsg->setMsg($msg);
        $pushmsg->setOperation($op);
        $pushmsg->setRoom($room);
        //发送到队列里
        producer()->produce(new Producer($pushmsg));
    }

    /**
     * broadcast
     * @param int $op
     * @param int $peed
     * @param $msg
     */
    public function broadcastMsg(int $op,int $speed,$msg)
    {
        $pushmsg = new PushMsg();
        $pushmsg->setOperation($op);
        $pushmsg->setSpeed($speed);
        $pushmsg->setMsg($msg);
        //发送到队列里
        \producer()->produce(new Producer($pushmsg));
    }

}