<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/17
 * Time: 20:21
 */

namespace App\Lib;


use ImQueue\Amqp\Message\ProducerMessage;

class Producer extends ProducerMessage
{
    public function __construct($data)
    {
        $this->setRoutingKey("im-logic");
        $this->setExchange("im-logic");
        $this->setPayload($data);
    }


}