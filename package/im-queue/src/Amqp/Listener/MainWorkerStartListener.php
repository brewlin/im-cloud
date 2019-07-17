<?php declare(strict_types=1);


namespace ImQueue\Amqp\Listener;


use ImQueue\Amqp\Message\ProducerMessageInterface;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 */
class MainWorkerStartListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ContainerInterface $container, StdoutLoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [
            MainWorkerStart::class,
        ];
    }

    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     */
    public function process(object $event)
    {
        // Declare exchange and routingKey
        $producerMessages = AnnotationCollector::getClassByAnnotation(Producer::class);
        if ($producerMessages) {
            $producer = $this->container->get(\ImQueue\Amqp\Producer::class);
            $instantiator = $this->container->get(Instantiator::class);
            /**
             * @var string
             * @var Producer $annotation
             */
            foreach ($producerMessages as $producerMessageClass => $annotation) {
                $instance = $instantiator->instantiate($producerMessageClass);
                if (! $instance instanceof ProducerMessageInterface) {
                    continue;
                }
                $annotation->exchange && $instance->setExchange($annotation->exchange);
                $annotation->routingKey && $instance->setRoutingKey($annotation->routingKey);
                try {
                    $producer->declare($instance);
                    $this->logger->debug(sprintf('AMQP exchange[%s] and routingKey[%s] were created successfully.', $instance->getExchange(), $instance->getRoutingKey()));
                } catch (AMQPProtocolChannelException $e) {
                    $this->logger->debug('AMQPProtocolChannelException: ' . $e->getMessage());
                    // Do nothing.
                }
            }
        }
    }
}
