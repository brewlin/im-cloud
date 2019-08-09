<?php

declare(strict_types=1);


namespace ImQueue\Pool;

use Co\Channel;
use Core\Container\Mapping\Bean;
use Core\Pool\PoolConnectionInterface;
use Core\Pool\PoolFactory;
use ImQueue\Amqp\Connection;

use InvalidArgumentException;

/**
 * Class AmqpConnectionPool
 * @package ImQueue\Pool
 * @Bean()
 */
class AmqpConnectionPool implements PoolConnectionInterface
{

    protected $config;
    /**
     * @var Connection
     */
    protected $connection;

    protected $name;

    public function init($config)
    {
        $this->config = $config;
//        $this->connection =  new Connection($this);
        $this->name = AmqpConnectionPool::class;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function getConfig(){
        return $this->config;
    }

    public function createConnection(): Connection
    {
        if(!$this->connection)
            $this->connection = new Connection($this);
        return $this->connection;
    }
    public function release(AmqpConnectionPool $pool){
        /** @var PoolFactory $pool */
        $poolFactory = container()->get(PoolFactory::class);
        $poolFactory->releasePool($pool);
    }

    /**
     * @param PoolFactory $pool
     */
    public function initPool(PoolFactory $pool)
    {

        $poolSize = (int)env("QUEUE_POOL_SIZE",10);
        $config = config("queue");
        $chan = new Channel($poolSize);
        for($i = 0 ; $i < $poolSize; $i++){
            $obj = new AmqpConnectionPool();
            $obj->init($config);
            $chan->push($obj);
        }
        $pool->registerPool(AmqpConnectionPool::class,$chan);
    }
}
