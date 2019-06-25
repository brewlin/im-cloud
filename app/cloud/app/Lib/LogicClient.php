<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/25 0025
 * Time: 下午 2:50
 */

namespace App\Lib;


use Core\Container\Container;
use Discovery\Balancer\RandomBalancer;
use Grpc\ChannelCredentials;

class LogicClient
{
    /**
     * servicelist
     * [
     *      "127.0.0.1:9500"
     * ]
     * @var array
     */
    public static $serviceList = [];

    /**
     * 返回一个可用的grpc 客户端 和logic 节点进行交互
     * @return \Im\Logic\LogicClient
     * @throws \Exception
     */
    public static function getLogicClient(){
        $node = Container::getInstance()->get(RandomBalancer::class)->select(self::$serviceList);
        $client = new \Im\Logic\LogicClient($node,[
            'credentials' => ChannelCredentials::createInsecure()
        ]);
        $client->start();
        return $client;
    }
}