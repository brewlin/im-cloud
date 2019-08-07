<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/17
 * Time: 20:21
 */

namespace App\Lib;


use Core\Container\Mapping\Bean;
use ImQueue\Amqp\Message\ProducerMessage;

/**
 * Class Producer
 * @package App\Lib
 * @Bean()
 */
class Producer extends ProducerMessage
{
    public function __construct()
    {
        $this->setRoutingKey(env("ROUTE_KEY"));
        $this->setExchange(env("EXCHANGE"));
    }

    /**
     * @param $data
     * @return $this
     */
    public function producer($data)
    {
        $this->setPayload($data);
        return $this;
    }


}