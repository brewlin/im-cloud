<?php

namespace Discovery\Balancer;
use Core\Concern\ContainerTrait;
use Core\Container\Mapping\Bean;


/**
 * 负载均衡
 * @Bean()
 */
class RandomBalancer implements BalancerInterface
{
    public function select(array $serviceList, ...$params)
    {
        $randIndex = array_rand($serviceList);
        return $serviceList[$randIndex];
    }
}
