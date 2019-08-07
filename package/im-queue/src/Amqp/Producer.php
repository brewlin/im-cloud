<?php

declare(strict_types=1);


namespace ImQueue\Amqp;

use Core\Container\Mapping\Bean;
use ImQueue\Amqp\Message\ProducerMessageInterface;
use ImQueue\Pool\AmqpConnectionPool;
use Log\Helper\Log;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class Producer
 * @package ImQueue\Amqp
 * @Bean()
 */
class Producer extends Builder
{
    public function produce(ProducerMessageInterface $producerMessage, bool $confirm = false, int $timeout = 5): bool
    {
        $result = false;
        $message = new AMQPMessage($producerMessage->payload(), $producerMessage->getProperties());
        /** @var AmqpConnectionPool $pool */
        $pool = $this->getConnectionPool(AmqpConnectionPool::class);
        /** @var \ImQueue\Amqp\Connection $connection */
        $connection = $pool->createConnection();
        if ($confirm) {
            $channel = $connection->getConfirmChannel();
        } else {
            $channel = $connection->getChannel();
        }
        $channel->set_ack_handler(function () use (&$result) {
            $result = true;
        });

        try {
            $channel->basic_publish($message, $producerMessage->getExchange(), $producerMessage->getRoutingKey());
            $channel->wait_for_pending_acks_returns($timeout);
        } catch (\Throwable $exception) {
            // Reconnect the connection before release.
            $connection->reconnect();
            Log::error("produce error msg:%s",$exception->getMessage());
        } finally {
            $pool->release($pool);
        }

        return $confirm ? $result : true;
    }

}
