<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/25 0025
 * Time: 下午 2:50
 */

namespace App\Lib;


use Core\Container\Container;
use Core\Container\Mapping\Bean;
use Discovery\Balancer\RandomBalancer;
use Grpc\ChannelCredentials;

/**
 * Class LogicClient
 * @Bean()
 * @package App\Lib
 */
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
     * @return mixed|null
     * @throws \Exception
     */
    public static function getLogicClient(){
        if(empty(self::$serviceList))
            throw new \Exception("not logic node find",0);
        $node = Container::getInstance()->get(RandomBalancer::class)->select(array_keys(self::$serviceList));
        return $node;
    }

    /**
     * @param array $server
     */
    public static function updateService(array $server)
    {
        foreach ($server as $ser) {
            if (!isset(self::$serviceList[$ser])) {
                self::$serviceList[$ser] = $ser;
            }
        }
        foreach (self::$serviceList as $k => $ser) {
            if (!in_array($ser, $server)) {
                unset(self::$serviceList[$k]);
            }
        }
    }
}