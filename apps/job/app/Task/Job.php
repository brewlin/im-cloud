<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/22 0022
 * Time: 下午 2:33
 */

namespace App\Task;

use Core\Co;
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use Im\Logic\PushMsg;
use Log\Helper\Log;

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
    public function push(array $data)
    {
        Log::info("job node push msgType:".$data['type']);
        /** @var PushMsg $pushMsg */
        $pushmsg = new PushMsg();
        foreach ($data as $key => $value){
            $method = 'set'.ucfirst($key);
            if(method_exists($pushmsg,$method)){
                $pushmsg->{$method}($value);
            }else{
                Log::error("pushmsg not exist method:".$method);
                return;
            }
        }
        $keys = [];
        foreach ($pushmsg->getKeys()->getIterator() as $v){
            $keys[] = $v;
        }
//        Co::create(function()use($pushmsg,$keys,$serverList){
            switch ($pushmsg->getType())
            {
                case PushMsg\Type::PUSH:
                    container()->get(PushKey::class)->push(
                        $pushmsg->getOperation(),
                        $pushmsg->getServer(),
                        $keys,
                        $pushmsg->getMsg()
                    );
                    break;
                case PushMsg\Type::ROOM:
                    container()->get(PushRoom::class)->push(
                        $pushmsg->getRoom(),
                        $pushmsg->getOperation(),
                        $pushmsg->getMsg()

                    );
                    break;
                case PushMsg\Type::BROADCAST:
                    container()->get(Broadcast::class)->push(
                        $pushmsg->getOperation(),
                        $pushmsg->getMsg()
                    );
                default:
            }
//        },false);


    }

}