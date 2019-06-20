<?php

namespace Discovery;

use Discovery\Balancer\RandomBalancer;
use Discovery\Balancer\BalancerInterface;

/**
 * the manager of balancer
 */
class BalancerSelector implements SelectorInterface
{
    /**
     * the name of random
     */
    const TYPE_RANDOM = 'random';

    /**
     * @var string
     */
    private $balancer = self::TYPE_RANDOM;

    /**
     * @var array
     */
    private $balancers = [

    ];

    public static $instance;
    public static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * get balancer
     *
     * @param string $type
     *
     * @return BalancerInterface
     */
    public function select(string $type = null)
    {
        if (empty($type)) {
            $type = $this->balancer;
        }
        $balancers = $this->mergeBalancers();
        if (!isset($balancers[$type])) {
            throw new \Exception(sprintf('Balancer %s does not exist', $type));
        }

        $balancerBeanName = $balancers[$type];
        return $balancerBeanName::getInstance();
    }

    /**
     * merge default and config packers
     *
     * @return array
     */
    private function mergeBalancers()
    {
        return array_merge($this->balancers, $this->defaultBalancers());
    }

    /**
     * the balancers of default
     *
     * @return array
     */
    private function defaultBalancers()
    {
        return [
            self::TYPE_RANDOM => RandomBalancer::class,
        ];
    }
}
