<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/22 0022
 * Time: 下午 2:33
 */

namespace App\Lib;

use Im\Logic\PushMsg;

/**
 * Class LogicPush
 * @package App\Lib
 */
class LogicPush
{
    /**
     * 消费 队列数据后分发
     * @param $pushmsg PushMsg
     */
    public function push($pushmsg)
    {
        switch ($pushmsg->getType())
        {
            case PushMsg\Type::PUSH:
            case PushMsg\Type::ROOM:
            case PushMsg\Type::BROADCAST:
            default:
        }

    }

}