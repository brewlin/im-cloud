<?php

declare(strict_types=1);


namespace ImRedis\Pool;

use \Swoole\Coroutine\Channel;
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
    /**
     * @var array $config
     */
    protected $config;
    /**
     * @var PhpRedisConnector
     */
    protected $connection;
    /**
     * @var
     */
    protected $name;

    /**
     * @param $config
     * @return $this
     */
    public function init($config)
    {
        $this->config = $config;
        $this->name = RedisConnectionPool::class;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getConfig(){
        return $this->config;
    }

    /**
     * @return \Redis
     */
    public function createConnection(): \Redis
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
     * @return RedisConnectionPool
     */
    public function create($options = ""){
        $config = config("redis");
        $obj = new RedisConnectionPool();

        return  $obj->init($config);
    }
}
