<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/20 0020
 * Time: 下午 12:01
 */
use Discovery\BalancerSelector;
use Discovery\ProviderSelector;
if (!function_exists('balancer')) {
    /**
     * @return \Discovery\BalancerSelector
     */
    function balancer(): BalancerSelector
    {
        return bean(BalancerSelector::class);
    }
}

if (!function_exists('provider')) {
    /**
     * @return \Discovery\ProviderSelector
     */
    function provider(): ProviderSelector
    {
        return bean(ProviderSelector::class);
    }
}