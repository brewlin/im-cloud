<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/4
 * Time: 10:14
 */
namespace Grpc\Client;
use Core\Pool\PoolFactory;
use Grpc\Pool\Connection;
use Grpc\Pool\LogicConnectionPool;

/**
 * Class GrpcLogicClient
 * @package Grpc\Client
 */
class GrpcLogicClient
{
    /**
     * @param string $serverId
     * @return LogicConnectionPool
     */
    public static function connection(){
        /** @var PoolFactory $pool */
        $pool = bean(PoolFactory::class);
        /** @var LogicConnectionPool $connectionPool */
        $connectionPool = $pool->getPool(LogicConnectionPool::class);
        return $connectionPool;
    }
    /**
     * Connect
     * @param \Im\Logic\ConnectReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public static function Connect(string $serverId,\Im\Logic\ConnectReq $argument, $metadata = [], $options = []) {
        if(empty($serverId))return;
        $pool = self::connection();
        $connection = $pool->getConnection($serverId);
        $client = $connection->getActiveConnection();
        $res = $client->Connect($argument,$metadata,$options);
        self::release($pool,$connection);
        return $res;
    }
    /**
     * Heartbeat
     * @param \Im\Logic\HeartbeatReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public static function Heartbeat(string $serverId,\Im\Logic\HeartbeatReq $argument, $metadata = [], $options = []) {
        if(empty($serverId))return;
        $pool = self::connection();
        $connection = $pool->getConnection($serverId);
        $client = $connection->getActiveConnection();
        $res = $client->Heartbeat($argument,$metadata,$options);
        self::release($pool,$connection);
        return $res;
    }

    /**
     * @param LogicConnectionPool $pool
     * @param Connection $con
     */
    public static function release(LogicConnectionPool $pool,Connection $con)
    {
        $pool->release($pool);
        $con->release();
    }

}