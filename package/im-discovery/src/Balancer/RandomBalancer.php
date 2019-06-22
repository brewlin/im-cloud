<?php

namespace Discovery\Balancer;
use Core\Concern\ContainerTrait;


/**
 * 负载均衡
 */
class RandomBalancer implements BalancerInterface
{
    public function select(array $serviceList, ...$params)
    {
        $randIndex = array_rand($serviceList);
        return $serviceList[$randIndex];
    }
}
