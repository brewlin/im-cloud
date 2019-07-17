<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/17
 * Time: 17:17
 */

namespace App\Lib;


use ImQueue\Amqp\Message\ConsumerMessage;
use ImQueue\Amqp\Message\ConsumerMessageInterface;

class Consumer extends ConsumerMessage
{
    public function __construct()
    {
        $this->setExchange("im-logic");
        $this->setQueue("im-logic");
        $this->setRoutingKey("im-logic");
    }

    public function consume($data): string
    {
        // TODO: Implement consume() method.
    }

}