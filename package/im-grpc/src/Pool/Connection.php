<?php

declare(strict_types=1);

namespace Grpc\Pool;

use Grpc\ChannelCredentials;
use Im\Cloud\CloudClient;
use Im\Logic\LogicClient;
use Core\Pool\ConnectionInterface;
use Core\Pool\PoolFactory;

class Connection implements ConnectionInterface
{
    /**
     * @var PoolFactory
     */
    protected $pool;

    /**
     * @var LogicClient | CloudClient
     */
    protected $connection;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var LogicClient | CloudClient
     */
    protected $clientType;

    /**
     * @var string
     */
    protected $serverId;

    protected $poolname;

    /**
     * Connection constructor.
     * @param string $clientType
     * @param string $serverId
     */
    public function __construct(string $clientType,string $serverId,PoolFactory $pool,string $poolname)
    {
        $this->poolname = $poolname;
        $this->pool = $pool;
        $this->clientType = $clientType;
        $this->serverId = $serverId;
        $this->initConnection();
    }

    public function __call($name, $arguments)
    {
        return $this->connection->{$name}(...$arguments);
    }

    /**
     * @return CloudClient|LogicClient
     */
    public function getActiveConnection()
    {
        if ($this->check()) {
            return $this->connection;
        }
        $this->reconnect();

        return $this->connection;
    }


    public function reconnect(): bool
    {
        $this->initConnection();

        return true;
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        if(empty($this->connection))return false;
        $this->connection->start();
        return true;
    }

    public function close(): bool
    {
        $this->connection->close();
        $this->channel = null;
        $this->confirmChannel = null;
        return true;
    }

    protected function initConnection():void
    {
        $this->connection = new $this->clientType($this->serverId,[
//            'credentials' => ChannelCredentials::createInsecure()
        ]);
    }

    public function release(): void
    {
        $this->pool->push($this->poolname,$this);
    }

    public function getConnection()
    {
        return $this->getActiveConnection();
    }
}
