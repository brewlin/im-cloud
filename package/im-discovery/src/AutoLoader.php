<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/22
 * Time: 16:11
 */

namespace Discovery;


use Core\Container\Container;
use Discovery\Balancer\RandomBalancer;
use Discovery\Balancer\RoundRobinBalancer;
use Discovery\Provider\ConsulProvider;

class AutoLoader implements \Core\Contract\Autoloader
{
    public function handler()
    {
        Container::getInstance()->create(RandomBalancer::class);
        Container::getInstance()->create(RoundRobinBalancer::class);
        Container::getInstance()->create(BalancerSelector::class);
        Container::getInstance()->create(ProviderSelector::class);
        Container::getInstance()->create(ConsulProvider::class);
        // TODO: Implement handler() method.
    }

}