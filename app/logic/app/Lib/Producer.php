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
        $this->setRoutingKey(env("ROUTE_KEY"));
        $this->setExchange(env("EXCHANGE"));
        $this->setPayload($data);
    }


}