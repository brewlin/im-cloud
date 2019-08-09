<?php
/**
 *
 */
namespace ImQueue;
use Core\Container\Mapping\Bean;
use ImQueue\Amqp\Consumer;
use ImQueue\Amqp\Producer;
use ImQueue\Pool\AmqpConnectionPool;

/**
 * @Bean()
 * Provider selector
 */
class QueueSelector implements SelectorInterface
{
    /**
     * type
     */
    const TYPE_QUEUE = [
        'amqp' => AmqpConnectionPool::class
    ];
    const DEFAULT_TYPE = 'amqp';

    /**
     * Default consumers
     *
     * @var array
     */
    private $consumers = [];

    /**
     * Default producers
     * @var array
     */
    private $producers = [];

    /**
     * @var array
     */
    private $providers
        = [

        ];
    /**
     * Select a provider by Selector
     *
     * @param string $type
     * @throws \InvalidArgumentException
     */
    public function select(string $type=null,$isConsumer = true)
    {
        $type = env("QUEUE_TYPE",self::DEFAULT_TYPE);
        $providers = $this->mergeQueue($isConsumer);

        if (!isset($providers[$type])) {
            throw new \InvalidArgumentException(sprintf('Provider %s does not exist', $type));
        }

        $providerBeanName = $providers[$type];
        return bean($providerBeanName);
    }

    /**
     * merge default and config packers
     *
     * @return array
     */
    private function mergeQueue($isConsumer = true)
    {
        $this->consumers =  array_merge($this->consumers, $this->defaultConsumers());
        $this->producers = array_merge($this->producers,$this->defaultProducers());

        if($isConsumer)return $this->consumers;
        return $this->producers;


    }

    /**
     * the balancers of default
     *
     * @return array
     */
    private function defaultConsumers()
    {
        return [
            "amqp" => Consumer::class,
        ];
    }
    private function defaultProducers()
    {
        return [
            "amqp" => Producer::class
        ];
    }
}
