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
    /**
     * @param string $type
     * @param int $n
     * @return array
     */
    public function onlineTop(string $type,int $n)
    {
        $tops = [];
        foreach (Logic::$roomCount as $key => $cnt){
            //string.hasPrefix
            if(strpos($key,$type) == 0){
                list($_,$roomId) = Room::decodeRoomKey($key);
                $tops[] = [
                    "roomId" => $roomId,
                    "count" => $cnt,
                ];
            }
        }

        $sortKey = array_column($tops,"count");
        array_multisort($sortKey,SORT_DESC,$tops);

        if(count($tops) > $n){
            $tops = array_slice($tops,0,$n);
        }
        return $tops;
    }

    /**
     * @param string $type
     * @param array $rooms
     * @return array
     */
    public function onlineRoom(string $type,array $rooms)
    {
        $res = [];
        foreach ($rooms as $room)
        {
            $res[$room] = Logic::$roomCount[Room::encodeRoomKey($type,$room)];
        }
        return $res;
    }
}