<?php
/**
 *
 */
namespace ImQueue;
use Core\Container\Mapping\Bean;

/**
 * @Bean()
 * Provider selector
 */
class QueueSelector implements SelectorInterface
{
    /**
     * consul
     */
    const TYPE_QUEUE = 'amqp';

    /**
     * Default provider
     *
     * @var string
     */
    private $provider = self::TYPE_QUEUE;

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
    public function select(string $type = null)
    {
        if (empty($type)) {
            $type = env("QUEUE_TYPE",$this->provider);
        }

        $providers = $this->mergeProviders();
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
    private function mergeProviders()
    {
        return array_merge($this->providers, $this->defaultProvivers());
    }

    /**
     * the balancers of default
     *
     * @return array
     */
    private function defaultProvivers()
    {
        return [
            self::TYPE_QUEUE => AMQPProvider::class,
        ];
    }
}
