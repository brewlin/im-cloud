<?php

declare(strict_types=1);


namespace ImRedis\Pool;

use Co\Channel;
use Core\Container\Mapping\Bean;
use Core\Pool\PoolConnectionInterface;
use Core\Pool\PoolFactory;

use ImRedis\Connector\PhpRedisConnector;
use ImRedis\RedisDb;
use InvalidArgumentException;

/**
 * @package ImQueue\Pool
 * @Bean()
 */
class RedisConnectionPool implements PoolConnectionInterface
{

    protected $config;
    /**
     * @var PhpRedisConnector
     */
    protected $connection;

    protected $name;

    public function init($config)
    {
        $this->config = $config;
//        $this->connection =  new Connection($this);
        $this->name = RedisConnectionPool::class;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function getConfig(){
        return $this->config;
    }

    public function createConnection(): PhpRedisConnector
    {
        if(!$this->connection)
            $this->connection = (new PhpRedisConnector())->connect($this->config,[]);
        return $this->connection;
    }
    public function release(RedisConnectionPool $pool){
        /** @var PoolFactory $pool */
        $poolFactory = container()->get(PoolFactory::class);
        $poolFactory->releasePool($pool);
    }

    /**
     * @param PoolFactory $pool
     */
    public function initPool(PoolFactory $pool)
    {

        $poolSize = (int)env("REDIS_POOL_SIZE",10);
        /** @var RedisDb $config */
        $config = \bean(RedisDb::class);
        $chan = new Channel($poolSize);
        for($i = 0 ; $i < $poolSize; $i++){
            $chan->push((new RedisConnectionPool())->init($config->getConfig()));
        }
        $pool->registerPool(RedisConnectionPool::class,$chan);
    }
}
