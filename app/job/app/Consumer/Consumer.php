<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/17
 * Time: 17:17
 */

namespace App\Consumer;


use App\Lib\CloudClient;
use App\Task\Job;
use Core\Co;
use Im\Logic\PushMsg;
use ImQueue\Amqp\Message\ConsumerMessage;
use ImQueue\Amqp\Result;
use Log\Helper\CLog;
use Task\Task;

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
        Co::create(function()use($data){
            if(empty(CloudClient::$serviceList)){
                Clog::error("cancle task deliver discovery cloud node is empty");
                return;
            }
            Task::deliver(Job::class,"push",[CloudClient::$serviceList,$data]);
        },false);
        return Result::ACK;
    }

    /**
     * 重新排队
     * @return bool
     */
    public function isRequeue(): bool
    {
        return true;
    }

}