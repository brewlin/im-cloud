<?php

declare(strict_types=1);

namespace ImQueue\Amqp;

use ImQueue\Amqp\Pool\PoolFactory;
use ImQueue\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerInterface;

class ConsumerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new Consumer($container, $container->get(PoolFactory::class), $container->get(StdoutLoggerInterface::class));
    }
}
