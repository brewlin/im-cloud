<?php declare(strict_types=1);


namespace ImQueue\Amqp;

use Core\Container\Mapping\Bean;
use ImQueue\Amqp\Exception\MessageException;
use ImQueue\Amqp\Message\ConsumerMessageInterface;
use ImQueue\Amqp\Message\MessageInterface;
use ImQueue\Pool\AmqpConnectionPool;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Log\Helper\CLog;
use Throwable;

/**
 * Class Consumer
 * @package ImQueue\Amqp
 * @Bean()
 */
class Consumer extends Builder
{
    /**
     * @var bool
     */
    protected $status = true;

    /**
     * @var LoggerInterface
     */

    public function consume(ConsumerMessageInterface $consumerMessage): void
    {
        CLog::info("start get amqp pool");
        $pool = $this->getConnectionPool(AmqpConnectionPool::class);
        /** @var \ImQueue\Amqp\Connection $connection */
        $connection = $pool->createConnection();
        $channel = $connection->getConfirmChannel();

        $this->declare($consumerMessage, $channel);

        $channel->basic_consume(
            $consumerMessage->getQueue(),
            $consumerMessage->getRoutingKey(),
            false,
            false,
            false,
            false,
            function (AMQPMessage $message) use ($consumerMessage) {
                $data = $consumerMessage->unserialize($message->getBody());
                /** @var AMQPChannel $channel */
                $channel = $message->delivery_info['channel'];
                $deliveryTag = $message->delivery_info['delivery_tag'];
                try {
                    $result = $consumerMessage->consume($data);
                    if ($result === Result::ACK) {
                        CLog::debug($deliveryTag.' aced.');
                        return $channel->basic_ack($deliveryTag);
                    }
                    if ($consumerMessage->isRequeue() && $result === Result::REQUEUE) {
                        Clog::debug($deliveryTag . ' requeued.');
                        return $channel->basic_reject($deliveryTag, true);
                    }
                } catch (Throwable $exception) {
                    CLog::debug($exception->getMessage());
                }
                CLog::debug($deliveryTag . ' rejected.');
                $channel->basic_reject($deliveryTag, false);
            }
        );

        while (count($channel->callbacks) > 0) {
            $channel->wait();
        }
        $pool->release($pool);
    }

    public function declare(MessageInterface $message, ?AMQPChannel $channel = null): void
    {
        if (! $message instanceof ConsumerMessageInterface) {
            throw new MessageException('Message must instanceof ' . ConsumerInterface::class);
        }

        if (! $channel) {
            $pool = $this->getConnectionPool($message->getPoolName());
            /** @var \ImQueue\Amqp\Connection $connection */
            $connection = $pool->createConnection();
            $channel = $connection->getChannel();
        }

        parent::declare($message, $channel);

        $builder = $message->getQueueBuilder();

        $channel->queue_declare($builder->getQueue(), $builder->isPassive(), $builder->isDurable(), $builder->isExclusive(), $builder->isAutoDelete(), $builder->isNowait(), $builder->getArguments(), $builder->getTicket());

        $channel->queue_bind($message->getQueue(), $message->getExchange(), $message->getRoutingKey());
    }
}
