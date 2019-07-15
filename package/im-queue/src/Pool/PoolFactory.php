<?php

declare(strict_types=1);


namespace ImQueue\Pool;

class PoolFactory
{
    /**
     * @var AmqpConnectionPool | KafakConnectionPool
     */
    public static $pools = [];

    public function __construct($name)
    {
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
