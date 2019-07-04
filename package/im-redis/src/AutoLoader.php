<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/22
 * Time: 16:11
 */

namespace ImRedis;


use Core\Container\Container;
use ImRedis\Connector\PhpRedisConnector;

class AutoLoader implements \Core\Contract\Autoloader
{
    public function handler()
    {
        Container::getInstance()->create(PhpRedisConnector::class);
        Container::getInstance()->create(RedisDb::class);
        // TODO: Implement handler() method.
    }

}