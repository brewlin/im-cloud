<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/17
 * Time: 17:17
 */

namespace App\Consumer;


use App\Lib\Job;
use Co\Client;
use Im\Logic\PushMsg;
use ImQueue\Amqp\Message\ConsumerMessage;
use Log\Helper\CLog;

/**
 * Class Consumer
 * @package App\Lib
 */
class Consumer extends ConsumerMessage
{
    public function __construct()
    {
        $this->setExchange(env("EXCHANGE"));
        $this->setQueue(env("QUEUE"));
        $this->setRoutingKey(env("ROUTE_KEY"));
    }

    /**
     * 主流程消费数据入口
     * @param PushMsg $data
     * @return string
     */
    public function consume($data): string
    {
        CLog::info("job node consume data:".json_encode($data));
        /** @var Job $job */
        $job = container()->get(Job::class);
        $pushMsg = new PushMsg($data);
        foreach ($data as $key => $value){
            $method = 'set'.ucfirst($key);
            if(method_exists($pushMsg,$method)){
                $pushMsg->{$method}($value);
            }else{
                CLog::error("pushmsg not exist method:".$method);
                return '';
            }
        }
        $job->push($pushMsg);
    }

}