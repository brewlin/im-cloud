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
use Log\Helper\Log;

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
        /** @var LogicConnectionPool $connectionPool */
        $connectionPool = bean(LogicConnectionPool::class);
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
        $res = null;
        try{
            $res = $client->Connect($argument,$metadata,$options);
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }
        self::release($pool,$connection);
        return $res;
    }
    /**
     * Disconnect
     * @param \Im\Logic\DisconnectReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public static function Disconnect(string $serverId ,\Im\Logic\DisconnectReq $argument, $metadata = [], $options = []) {
        if(empty($serverId))return;
        $pool = self::connection();
        $connection = $pool->getConnection($serverId);
        $client = $connection->getActiveConnection();
        $res = null;
        try{
            $res = $client->Disconnect($argument,$metadata,$options);
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }
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
        $res = null;
        try{
            $res = $client->Heartbeat($argument,$metadata,$options);
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }
        self::release($pool,$connection);
        return $res;
    }

    /**
     * @param string $serverId
     * @param \Im\Logic\PushKeysReq $argument
     * @param array $metadata
     * @param array $options
     * @return array|\Google\Protobuf\Internal\Message[]|\Grpc\StringifyAble[]|null|\swoole_http2_response[]|void
     */
    public static function PushKeys(string $serverId,\Im\Logic\PushKeysReq $argument, $metadata = [], $options = []) {
        if(empty($serverId))return;
        $pool = self::connection();
        $connection = $pool->getConnection($serverId);
        $client = $connection->getActiveConnection();
        $res = null;
        try{
            $res = $client->PushKeys($argument,$metadata,$options);
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }
        self::release($pool,$connection);
        return $res;
    }

    /**
     * @param string $serverId
     * @param \Im\Logic\PushMidsReq $argument
     * @param array $metadata
     * @param array $options
     * @return array|\Google\Protobuf\Internal\Message[]|\Grpc\StringifyAble[]|null|\swoole_http2_response[]|void
     */
    public static function PushMids(string $serverId,\Im\Logic\PushMidsReq $argument, $metadata = [], $options = []) {
        if(empty($serverId))return;
        $pool = self::connection();
        $connection = $pool->getConnection($serverId);
        $client = $connection->getActiveConnection();
        $res = null;
        try{
            $res = $client->PushMids($argument,$metadata,$options);
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }
        self::release($pool,$connection);
        return $res;
    }

    /**
     * @param string $serverId
     * @param \Im\Logic\PushRoomReq $argument
     * @param array $metadata
     * @param array $options
     * @return array|\Google\Protobuf\Internal\Message[]|\Grpc\StringifyAble[]|null|\swoole_http2_response[]|void
     */
    public static function PushRoom(string $serverId,\Im\Logic\PushRoomReq $argument, $metadata = [], $options = []) {
        if(empty($serverId))return;
        $pool = self::connection();
        $connection = $pool->getConnection($serverId);
        $client = $connection->getActiveConnection();
        $res = null;
        try{
            $res = $client->PushRoom($argument,$metadata,$options);
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }
        self::release($pool,$connection);
        return $res;
    }

    /**
     * @param string $serverId
     * @param \Im\Logic\PushAllReq $argument
     * @param array $metadata
     * @param array $options
     * @return array|\Google\Protobuf\Internal\Message[]|\Grpc\StringifyAble[]|null|\swoole_http2_response[]|void
     */
    public static function PushAll(string $serverId,\Im\Logic\PushAllReq $argument, $metadata = [], $options = []) {
        if(empty($serverId))return;
        $pool = self::connection();
        $connection = $pool->getConnection($serverId);
        $client = $connection->getActiveConnection();
        $res = null;
        try{
            $res = $client->PushAll($argument,$metadata,$options);
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }
        self::release($pool,$connection);
        return $res;
    }

    /**
     * @param LogicConnectionPool $pool
     * @param Connection $con
     */
    public static function release(LogicConnectionPool $pool,Connection $con)
    {
//        $pool->release($pool);
        $con->release();
    }

}