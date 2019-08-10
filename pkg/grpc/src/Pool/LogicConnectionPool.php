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
class LogicConnectionPool implements PoolConnectionInterface
{


    const Poolname = "im-grpc-logic-serverId-%s";

    protected $name = LogicConnectionPool::class;


    public function getName(): string
    {
        return $this->name;
    }
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
    public function createPool(string $serverId,PoolFactory $pool)
    {
        $poolname = sprintf(self::Poolname,$serverId);
        $poolSize = (int)env("GRPC_POOL_SIZE",10);
        $chan = new Channel($poolSize);
        for($i = 0 ; $i < $poolSize; $i++){
            $obj = new Connection(\Im\Logic\LogicClient::class,$serverId,$pool,$poolname);
            $chan->push($obj);
        }
        $pool->registerPool($poolname,$chan);

    }
    public function createConnection(): Connection
    {
    }
    public function release(LogicConnectionPool $pool){
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
