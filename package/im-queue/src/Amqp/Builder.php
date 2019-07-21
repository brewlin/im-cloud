<?php

declare(strict_types=1);

namespace ImQueue\Amqp;

use Core\Pool\PoolConnectionInterface;
use Core\Pool\PoolFactory;
use ImQueue\Amqp\Message\MessageInterface;
use ImQueue\Pool\AmqpConnectionPool;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use Psr\Container\ContainerInterface;

class Builder
{
    protected $name = 'default';


    /**
     * @throws AMQPProtocolChannelException when the channel operation is failed
     */
    public function declare(MessageInterface $message, ?AMQPChannel $channel = null): void
    {
        if (! $channel) {
            $pool = $this->getConnectionPool($message->getPoolName());
            /** @var \ImQueue\Amqp\Connection $connection */
            $connection = $pool->createConnection();
            $channel = $connection->getChannel();
        }

        $builder = $message->getExchangeBuilder();

        $channel->exchange_declare($builder->getExchange(), $builder->getType(), $builder->isPassive(), $builder->isDurable(), $builder->isAutoDelete(), $builder->isInternal(), $builder->isNowait(), $builder->getArguments(), $builder->getTicket());
    }

    /**
     * @param string $poolName
     * @return PoolConnectionInterface
     */
    protected function getConnectionPool(string $poolName)
    {
        /** @var PoolFactory $pool */
        $pool = bean(\Core\Pool\PoolFactory::class);
        return $pool->getPool($poolName);
    }
}
