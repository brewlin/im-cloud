<?php

namespace Discovery;
use Core\Container\Mapping\Bean;
use Discovery\Provider\ConsulProvider;
use Discovery\Provider\ProviderInterface;

/**
 * @Bean()
 * Provider selector
 */
class ProviderSelector implements SelectorInterface
{
    /**
     * consul
     */
    const TYPE_CONSUL = 'consul';

    /**
     * Default provider
     *
     * @var string
     */
    private $provider = self::TYPE_CONSUL;

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
     * @return ProviderInterface
     * @throws \InvalidArgumentException
     */
    public function select(string $type = null)
    {
        if (empty($type)) {
            $type = env("DISCOVERY_TYPE",$this->provider);
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
            self::TYPE_CONSUL => ConsulProvider::class,
        ];
    }
}
