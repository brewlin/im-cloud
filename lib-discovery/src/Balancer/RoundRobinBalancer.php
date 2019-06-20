<?php

namespace Discovery\Balancer;


/**
 * 轮询负载
 */
class RoundRobinBalancer implements BalancerInterface
{
    public static $instance;
    public static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    private $lastIndex = 0;

    public function select(array $serviceList, ...$params)
    {
        $currentIndex = $this->lastIndex;
        $value = $serviceList[$currentIndex];
        if ($currentIndex + 1 > count($serviceList) - 1) {
            $this->lastIndex = 0;
        } else {
            $this->lastIndex++;
        }
        return $value;
    }
}
