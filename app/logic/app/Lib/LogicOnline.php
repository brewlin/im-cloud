<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/10
 * Time: 23:05
 */

namespace App\Lib;


use App\Service\Dao\QueueDao;
use App\Service\Dao\RedisDao;
use App\Service\Model\Room;
use Core\Container\Container;
use Core\Container\Mapping\Bean;

/**
 * @package lib
 * @Bean()
 */
class LogicOnline
{
    public function onlineTop(int $op,array $keys,$msg)
    {
        /** @var RedisDao $servers */
        $servers = \container()->get(RedisDao::class)->getServersByKeys($keys);
        $pushKeys = [];
        foreach ($keys as $i => $key){
            $server = $servers[$i];
            if(!empty($server) && empty($key))
                $pushKeys[$server][] = $key;
        }
        foreach ($pushKeys as $server => $key){
            //丢到队列里去操做，让job去处理
            \container()->get(QueueDao::class)->pushMsg($op,$server,$pushKeys[$server],$msg);
        }
    }
}