<?php

declare(strict_types=1);

use Core\Container\Mapping\Bean;
namespace ImQueue\Pool;
/**
 * @Bean()
 */
class PoolFactory
{
    /**
     * @var AmqpConnectionPool | KafakConnectionPool
     */
    public static $pools = [];

    public function __construct()
    {
        $type = env("QUEUE_TYPE","amqp");
        $name = QueueSelector::QUEUE_TYPE[$type];

        $poolSize = env("QUEUE_POOL_SIZE",10);
        PoolFactory::$pools[$name] = new \chan($poolSize);
        for($i = 0 ; $i <= $poolSize; $i++){
            PoolFactory::$pools[$name]->push(new $name);
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
}
