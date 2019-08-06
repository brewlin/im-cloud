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
use Log\Helper\Log;

class CloudClient
{
    /**
     * @var string
     */
    const ServerList = "im-job-serverlist";
    /**
     * servicelist
     * [
     *      "127.0.0.1:9500" => "127.0.1:9500"
     * ]
     * @var array
     */
    public static $serviceList = [];

    /**
     * 返回一个可用的grpc 客户端 和logic 节点进行交互
     * @return mixed|null
     * @throws \Exception
     */
    public static function getCloudClient($serverId = ""){
        //push
        if($serverId)
            $node = self::$serviceList[$serverId];
        else//broadcast && broadcastroom
            $node = Container::getInstance()->get(RandomBalancer::class)->select(array_keys(self::$serviceList));
        if(empty($node)){
            Log::error("serverid:$serverId not find any node instance");
            return null;
        }
        return $node;
    }

    /**
     * @param $server
     */
    public static function updateService(array $server)
    {
        foreach ($server as $ser){
            if(!isset(self::$serviceList[$ser])){
                self::$serviceList[$ser] = $ser;
            }
        }
        foreach (self::$serviceList as $k => $ser){
            if(!in_array($ser,$server)){
               unset(self::$serviceList[$k]);
            }
        }
//        self::$serviceList = $server;
    }
}