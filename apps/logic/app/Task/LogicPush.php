<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/1
 * Time: 23:05
 */

namespace App\Task;


use App\Service\Dao\QueueDao;
use App\Service\Dao\RedisDao;
use App\Service\Model\Room;
use Core\Co;
use Core\Container\Container;
use Core\Container\Mapping\Bean;
use Swoole\Coroutine;

/**
 * @package lib
 * @Bean()
 */
class LogicPush
{
    /**
     * @param int $op
     * @param array $keys
     * @param $msg
     * @throws \Exception
     */
    public function pushKeys(int $op,array $keys,$msg)
    {
//        Co::create(function()use($op,$keys,$msg){
            /** @var RedisDao $servers */
            if(!($servers = \container()->get(RedisDao::class)->getServersByKeys($keys)))return;
            $pushKeys = [];
            foreach ($keys as $i => $key){
                $server = $servers[$i];
                if(!empty($server) && !empty($key))
                    $pushKeys[$server][] = $key;
            }
            foreach ($pushKeys as $server => $key){
                //丢到队列里去操做，让job去处理
                \container()->get(QueueDao::class)->pushMsg($op,$server,$pushKeys[$server],$msg);
            }
//        });
    }

    /**
     * @param int $op
     * @param array $mids
     * @param $msg
     * @throws \Exception
     */
    public function pushMids(int $op,array $mids,$msg)
    {
//        Co::create(function ()use($op,$mids,$msg){
            /** @var RedisDao $servers */
            $servers = \container()->get(RedisDao::class)->getKeysByMids($mids);
            $keys = [];
            foreach($servers as $key => $server){
                $keys[$server][] = $key;
            }
            foreach($keys as $server => $key){
                //丢到队列里去操做，让job去处理
                \container()->get(QueueDao::class)->pushMsg($op,$server,$key,$msg);
            }

//        },true);

    }

    /**
     * @param int $op
     * @param $type
     * @param $room
     * @param $msg
     * @throws \Exception
     */
    public function pushRoom(int $op,$type,$room,$msg)
    {
        Co::create(function ()use($op,$type,$room,$msg){
            \container()
                ->get(QueueDao::class)
                ->broadcastRoomMsg($op,\bean(Room::class)
                    ->encodeRoomKey($type,$room),$msg);
        });
    }

    /**
     * @param int $op
     * @param int $speed
     * @param $msg
     */
    public function pushAll(int $op,$msg)
    {
        \container()
            ->get(QueueDao::class)
            ->broadcastMsg($op,$msg);
    }
    

}