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
use Log\Helper\Log;

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
        /** @var CloudConnectionPool $pool */
        $connectionPool = bean(CloudConnectionPool::class);
        return $connectionPool;
    }

    /**
     * @param string $serverId
     * @param \Im\Cloud\PushMsgReq $argument
     * @param array $metadata
     * @param array $options
     * @return array|\Google\Protobuf\Internal\Message[]|\Grpc\StringifyAble[]|\swoole_http2_response[]
     */
    public static function PushMsg(string $serverId,\Im\Cloud\PushMsgReq $argument, $metadata = [], $options = [])
    {
        $pool = self::connection($serverId);
        $connection = $pool->getConnection($serverId);
        $client = $connection->getActiveConnection();
        $res = null;
        try{
            $res = $client->PushMsg($argument,$metadata,$options);
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }
        self::release($pool,$connection);
        return $res;
    }
    /**
     * Broadcast send to every enrity
     * @param \Im\Cloud\BroadcastReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public static function Broadcast(string $serverId,\Im\Cloud\BroadcastReq $argument, $metadata = [], $options = []) {
        $pool = self::connection($serverId);
        $connection = $pool->getConnection($serverId);
        $client = $connection->getActiveConnection();
        $res = null;
        try{
            $res = $client->Broadcast($argument,$metadata,$options);
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }
        self::release($pool,$connection);
        return $res;
    }
    /**
     * BroadcastRoom broadcast to one room
     * @param \Im\Cloud\BroadcastRoomReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public static function BroadcastRoom(string $serverId,\Im\Cloud\BroadcastRoomReq $argument,
                                  $metadata = [], $options = []) {
        $pool = self::connection($serverId);
        $connection = $pool->getConnection($serverId);
        $client = $connection->getActiveConnection();
        $res = null;
        try{
            $res = $client->BroadcastRoom($argument,$metadata,$options);
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }
        self::release($pool,$connection);
        return $res;
    }
    /**
     * @param CloudConnectionPool $pool
     * @param Connection $con
     */
    public static function release(CloudConnectionPool $pool,Connection $con)
    {
//        $pool->release($pool);
        $con->release();
    }

}