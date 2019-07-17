<?php

declare(strict_types=1);

namespace ImQueue\Pool;
use Core\Container\Mapping\Bean;
use ImQueue\QueueSelector;

/**
 * Class PoolFactory
 * @Bean()
 * @package ImQueue\Pool
 */
class PoolFactory
{
    /**
     * @var AmqpConnectionPool | KafakConnectionPool
     */
    public static $pools = [];

    public static function initPool()
    {
        $type = env("QUEUE_TYPE","amqp");
        $name = QueueSelector::TYPE_QUEUE[$type];

        $poolSize = env("QUEUE_POOL_SIZE",10);
        $config = require ROOT."/config/queue.php";
        PoolFactory::$pools[$name] = new \chan($poolSize);
        for($i = 0 ; $i <= $poolSize; $i++){
            PoolFactory::$pools[$name]->push(new $name($config));
        }
    }

    /**
     * @param string $name
     * @return AmqpConnectionPool|
     */
    public static function getPool(string $name): AmqpConnectionPool
    {
        if (isset(self::$pools[$name])) {
            return self::$pools[$name]->pop();
        }
    }

    /**
     * @param AmqpConnectionPool
     */
    public static function releasePool($pool){
        if (isset(self::$pools[$pool->getName()])) {
            return self::$pools[$pool->getName()]->push($pool);
        }
    }
}
