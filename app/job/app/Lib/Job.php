<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/22 0022
 * Time: 下午 2:33
 */

namespace App\Lib;

use Core\Container\Mapping\Bean;
use Im\Logic\PushMsg;
use Log\Helper\CLog;

/**
 * Class Job
 * @package App\Lib
 * @Bean()
 */
class Job
{
    /**
     * 消费 队列数据后分发
     * @param $pushmsg PushMsg
     */
    public function push($pushmsg)
    {
        CLog::info("job node push msg:".$pushmsg->getType());
        switch ($pushmsg->getType())
        {
            case PushMsg\Type::PUSH:
                container()->get(PushKey::class)->push(
                    $pushmsg->getOperation(),
                    $pushmsg->getServer(),
                    $pushmsg->getKeys(),
                    $pushmsg->getMsg()
                );
            case PushMsg\Type::ROOM:
                container()->get(PushRoom::class)->push(
                    $pushmsg->getRoom(),
                    $pushmsg->getOperation(),
                    $pushmsg->getMsg()

                );
            case PushMsg\Type::BROADCAST:
                container()->get(Broadcast::class)->push(
                    $pushmsg->getOperation(),
                    $pushmsg->getMsg(),
                    $pushmsg->getSpeed()
                );
            default:
        }

    }

}