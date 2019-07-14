<?php

declare(strict_types=1);


namespace ImQueue\Amqp\Pool;

use ImQueue\Di\Container;
use Psr\Container\ContainerInterface;

class PoolFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var AmqpConnectionPool[]
     */
    protected $pools = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getPool(string $name): AmqpConnectionPool
    {
        if (isset($this->pools[$name])) {
            return $this->pools[$name];
        }

        if ($this->container instanceof Container) {
            $pool = $this->container->make(AmqpConnectionPool::class, ['name' => $name]);
        } else {
            $pool = new AmqpConnectionPool($this->container, $name);
        }

        return $this->pools[$name] = $pool;
    }
}
