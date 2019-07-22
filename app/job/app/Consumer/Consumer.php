<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/17
 * Time: 17:17
 */

namespace App\Consumer;


use App\Lib\Job;
use Im\Logic\PushMsg;
use ImQueue\Amqp\Message\ConsumerMessage;

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
        /** @var Job $job */
        $job = container()->get(Job::class);
        $job->push($data);
    }

}