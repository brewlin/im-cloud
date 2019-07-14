<?php

declare(strict_types=1);


namespace ImQueue\Amqp;


use ImQueue\Amqp\Annotation\Consumer as ConsumerAnnotation;
use ImQueue\Amqp\Message\ConsumerMessageInterface;
use ImQueue\Di\Annotation\AnnotationCollector;
use ImQueue\Process\AbstractProcess;
use ImQueue\Process\ProcessManager;
use Psr\Container\ContainerInterface;

class ConsumerManager
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        $classes = AnnotationCollector::getClassByAnnotation(ConsumerAnnotation::class);
        $instantiator = $this->container->get(Instantiator::class);
        /**
         * @var string
         * @var ConsumerAnnotation $annotation
         */
        foreach ($classes as $class => $annotation) {
            $instance = $instantiator->instantiate($class);
            if (! $instance instanceof ConsumerMessageInterface) {
                continue;
            }
            $annotation->exchange && $instance->setExchange($annotation->exchange);
            $annotation->routingKey && $instance->setRoutingKey($annotation->routingKey);
            $annotation->queue && $instance->setQueue($annotation->queue);
            property_exists($instance, 'container') && $instance->container = $this->container;
            $nums = $annotation->nums;
            $process = $this->createProcess($instance);
            $process->nums = (int) $nums;
            $process->name = 'Consumer-' . $instance->getQueue();
            ProcessManager::register($process);
        }
    }

    private function createProcess(ConsumerMessageInterface $consumerMessage): AbstractProcess
    {
        return new class($this->container, $consumerMessage) extends AbstractProcess {
            /**
             * @var \ImQueue\Amqp\Consumer
             */
            private $consumer;

            /**
             * @var ConsumerMessageInterface
             */
            private $consumerMessage;

            public function __construct(ContainerInterface $container, ConsumerMessageInterface $consumerMessage)
            {
                parent::__construct($container);
                $this->consumer = $container->get(Consumer::class);
                $this->consumerMessage = $consumerMessage;
            }

            public function handle(): void
            {
                $this->consumer->consume($this->consumerMessage);
            }
        };
    }
}
