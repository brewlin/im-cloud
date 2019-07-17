<?php

declare(strict_types=1);


namespace ImQueue\Pool;

use ImQueue\Amqp\Connection;

use InvalidArgumentException;

class AmqpConnectionPool
{

    protected $config;
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    protected $name;

    public function __construct($config)
    {
        $this->config = $config;
        $this->connection =  new Connection($this);
        $this->name = AmqpConnectionPool::class;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function getConfig(){
        return $this->config;
    }

    protected function createConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}
