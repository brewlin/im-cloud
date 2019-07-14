<?php

declare(strict_types=1);


namespace ImQueue\Amqp;

use ImQueue\Contract\ConnectionInterface;
use ImQueue\Contract\StdoutLoggerInterface;
use ImQueue\Utils\Context as RequestContext;
use Psr\Container\ContainerInterface;

class Context
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    /**
     * Get a connection from request context.
     */
    public function connection(string $name): ?ConnectionInterface
    {
        $connections = [];
        if (RequestContext::has('amqp')) {
            $connections = RequestContext::get('amqp');
        }

        if (isset($connections[$name]) && $connections[$name] instanceof ConnectionInterface) {
            return $connections[$name];
        }

        return null;
    }

    /**
     * @return ConnectionInterface[]
     */
    public function connections(): array
    {
        $connections = [];
        if (RequestContext::has('amqp')) {
            $connections = RequestContext::get('amqp');
        }

        return $connections;
    }

    public function set($name, ConnectionInterface $connection): void
    {
        $connections = $this->connections();
        $connections[$name] = $connection;
        RequestContext::set('amqp', $connections);
    }
}
