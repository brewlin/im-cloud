<?php

namespace Discovery\Balancer;

/**
 * the balancer of connect pool
 */
interface BalancerInterface
{
    public function select(array $serviceList, ...$params);
}
