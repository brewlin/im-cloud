<?php

declare(strict_types=1);


namespace Grpc\Pool;

use App\Lib\LogicClient;
use \Swoole\Coroutine\Channel;
use Core\Container\Mapping\Bean;
use Core\Pool\PoolConnectionInterface;
use Core\Pool\PoolFactory;
use InvalidArgumentException;

/**
 * Class AmqpConnectionPool
 * @package Grpc\Pool
 * @Bean()
 */
class CloudConnectionPool implements PoolConnectionInterface
{


    /**
     * @var string
     */
    protected $name = CloudConnectionPool::class;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $serverId
     * @return Connection
     */
    public function getConnection(string $serverId):Connection
    {
        if(empty($serverId))return null;
        /** @var PoolFactory $pool */
        $pool = \bean(PoolFactory::class);
        return $pool->getPool($this->name,$serverId);

    }

    /**
     * @param string $serverId
     * @return PoolConnectionInterface|Connection
     */
    public function create($serverId = "")
    {
        /** @var PoolFactory $pool */
        $pool = bean(PoolFactory::class);
        $poolName = $serverId.static::class;

        $obj = new Connection(
            \Im\Cloud\CloudClient::class,
            $serverId,
            $pool,
            $poolName
            );
        return $obj;
    }
    public function createConnection(): Connection
    {
    }
}
