<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/17
 * Time: 16:18
 */

if(!function_exists("consumer")){
    /**
     * 返回一个消费者
     * @return \ImQueue\Amqp\Consumer
     */
    function consumer()
    {
        return container()->get(\ImQueue\QueueSelector::class)->select(null);
    }
}

if(!function_exists("producer")){
    /**
     * @return \ImQueue\Amqp\Producer
     */
    function producer()
    {
        return container()->get(\ImQueue\QueueSelector::class)->select(null,false);
    }
}