<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/5 0005
 * Time: 下午 4:01
 */

namespace App\Service\Dao;


use Im\Cloud\Proto;
use Im\Logic\PushMsg;

class QueueDao
{
    public function pushMsg(int $op,string $server,array $keys, $msg)
    {
        $pushmsg = new PushMsg();
        $pushmsg->setType(PushMsg\Type::PUSH);
        $pushmsg->setOperation($op);
        $pushmsg->setServer($server);
        $pushmsg->setKeys($keys);
        $pushmsg->setMsg($msg);
    }

}