<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/25 0025
 * Time: 下午 2:50
 */

namespace App\Lib;


use Core\Container\Mapping\Bean;
use Discovery\Balancer\RandomBalancer;
use Log\Helper\Log;
use Memory\Table;
use Memory\Table\MemoryTable;
use Memory\Table\Type;

/**
 * Class CloudClient
 * @package App\Lib
 * @Bean()
 */
class CloudClient
{
    /**
     * @var string
     */
    const ServerList = "im-job-serverlist";
    /**
     * servicelist
     * @var MemoryTable
     * [
     *   ip => [addr => ip]
     *   "127.0.0.1:9500" => ["Address" => "127.0.0.1","Port" => "9500"]
     * ]
     */
    public static $table = null;
    /**
     * CloudClient constructor.
     */
    public function __construct()
    {
        $memorySize = (int)env("MEMORY_TABLE",1000);
        $column = [
            "Address" => [Type::String,20],
            "Port"    => [Type::String,10],
        ];
        self::$table = Table::create($memorySize,$column);
    }
    /**
     * 返回一个可用的grpc 客户端 和logic 节点进行交互
     * @return mixed|null
     */
    public static function getCloudClient($serverId = ""){
        if(empty(self::$table->count() == 0)){
            Log::error("not cloud node find");
            return false;
        }
        //push
        if($serverId)
            $node = self::$table->get($serverId);
        else//broadcast && broadcastroom
            $node = bean(RandomBalancer::class)->select(self::$table->getKeys());
        if(empty($node)){
            Log::error("serverid:$serverId not find any node instance");
            return false;
        }
        return $node;
    }

    /**
     * automic operation insert|update|del
     * @param array $server
     */
    public static function updateService(array $server)
    {
        //insert if not exist | update if not equal
        $serverList = [];
        foreach ($server as $ser) {
            $addr = $ser["Address"].":".$ser["Port"];
            $serverList[] = $addr;
            if(!self::$table->exist($addr))
                self::$table->set($addr,$ser);
        }
        //del not exist
        foreach (self::$table as $k => $ser) {
            if (!in_array($k, $serverList)) {
                self::$table->del($k);
            }
        }
    }

    /**
     * @return array
     */
    public static function getAllInstance():array
    {
        return self::$table->getKeys();
    }
}