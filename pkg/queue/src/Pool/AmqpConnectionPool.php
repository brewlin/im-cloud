<?php

declare(strict_types=1);


namespace ImQueue\Pool;

use Core\Container\Mapping\Bean;
use Core\Pool\PoolConnectionInterface;
use Core\Pool\PoolFactory;
use ImQueue\Amqp\Connection;

use InvalidArgumentException;
use Swoole\Coroutine\Channel;

/**
 * Class AmqpConnectionPool
 * @package ImQueue\Pool
 * @Bean()
 */
class AmqpConnectionPool implements PoolConnectionInterface
{
    /**
     * @var array
     */
    protected $config;
    /**
     * @var Connection
     */
    protected $connection;
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @param $config
     */
    public function init($config)
    {
        $this->config = $config;
        $this->name = AmqpConnectionPool::class;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getConfig(){
        return $this->config;
    }

    /**
     * @return Connection
     */
    public function createConnection(): Connection
    {
        if(!$this->connection)
            $this->connection = new Connection($this);
        return $this->connection;
    }

    /**
     * @param AmqpConnectionPool $pool
     * @throws \Exception
     */
    public function release(AmqpConnectionPool $pool){
        /** @var PoolFactory $pool */
        $poolFactory = container()->get(PoolFactory::class);
        $poolFactory->releasePool($pool);
    }

    /**
     * @return AmqpConnectionPool
     */
    public function create($options = "")
    {
        $config = config("queue");
        $obj = new AmqpConnectionPool();
        $obj->init($config);
        return $obj;

    }
}
