<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/4
 * Time: 10:11
 */
namespace Grpc\Client;
use Core\Pool\PoolConnectionInterface;
use Core\Pool\PoolFactory;
use Grpc\Pool\CloudConnectionPool;
use Grpc\Pool\Connection;

/**
 * Class GrpcCloudClient
 * @package Grpc\Client
 */
class GrpcCloudClient
{
    /**
     * @param string $serverId
     * @return CloudConnectionPool
     */
    public static function connection(string $serverId){
        /** @var PoolFactory $pool */
        $pool = bean(PoolFactory::class);
        /** @var CloudConnectionPool $connectionPool */
        $connectionPool = $pool->getPool(CloudConnectionPool::class);
        return $connectionPool;
    }
    public static function PushMsg(string $serverId,\Im\Cloud\PushMsgReq $argument, $metadata = [], $options = [])
    {
        $pool = self::connection($serverId);
        $connection = $pool->getConnection($serverId);
        $client = $connection->getActiveConnection();
        $res = $client->PushMsg($argument,$metadata,$options);
        self::release($pool,$connection);
        return $res;
    }
    public static function release(CloudConnectionPool $pool,Connection $con)
    {
        $pool->release($pool);
        $con->release();
    }

}