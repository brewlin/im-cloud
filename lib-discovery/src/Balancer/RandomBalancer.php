<?php

namespace Discovery\Balancer;


/**
 * 负载均衡
 */
class RandomBalancer implements BalancerInterface
{
    public static $instance;
    public static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function select(array $serviceList, ...$params)
    {
        $randIndex = array_rand($serviceList);
        return $serviceList[$randIndex];
    }
}
