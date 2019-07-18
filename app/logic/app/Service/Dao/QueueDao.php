<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/5 0005
 * Time: 下午 4:01
 */

namespace App\Service\Dao;


use App\Lib\Producer;
use Im\Cloud\Proto;
use Im\Logic\PushMsg;

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
    public function pushMsg(int $op,string $server,array $keys, $msg)
    {
        $pushmsg = new PushMsg();
        $pushmsg->setType(PushMsg\Type::PUSH);
        $pushmsg->setOperation($op);
        $pushmsg->setServer($server);
        $pushmsg->setKeys($keys);
        $pushmsg->setMsg($msg);
        //发送到队列里
        producer()->produce(new Producer(serialize($pushmsg)));
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
        producer()->produce(new Producer(serialize($pushmsg)));
    }

}