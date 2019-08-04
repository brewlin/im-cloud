<?php

declare(strict_types=1);


namespace Grpc\Pool;

use App\Lib\LogicClient;
use Co\Channel;
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


    const Poolname = "im-grpc-cloud-serverId-%s";

    protected $name = CloudConnectionPool::class;


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
        $poolname = sprintf(self::Poolname,$serverId);
        /** @var PoolFactory $pool */
        $pool = \bean(PoolFactory::class);
        if(!$pool->check($poolname))
            $this->createPool($serverId,$pool);
        return $pool->pop($poolname);

    }
    private function createPool(string $serverId,PoolFactory $pool)
    {
        $poolname = sprintf(self::Poolname,$serverId);
        $poolSize = (int)env("GRPC_POOL_SIZE",10);
        $chan = new Channel($poolSize);
        for($i = 0 ; $i < $poolSize; $i++){
            $obj = new Connection(\Im\Cloud\CloudClient::class,$serverId,$pool,$poolname);
            $chan->push($obj);
        }
        $pool->registerPool($poolname,$chan);

    }
    public function createConnection(): Connection
    {
    }
    public function release(CloudConnectionPool $pool){
        /** @var PoolFactory $pool */
        $poolFactory = container()->get(PoolFactory::class);
        $poolFactory->releasePool($pool);
    }
    /**
     * @param PoolFactory $pool
     */
    public function initPool(PoolFactory $pool)
    {

        $poolSize = (int)env("GRPC_POOL_SIZE",10);
        $chan = new Channel($poolSize);
        for($i = 0 ; $i < $poolSize; $i++){
            $obj = new LogicConnectionPool();
            $chan->push($obj);
        }
        $pool->registerPool(LogicConnectionPool::class,$chan);
    }
}
