<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/28
 * Time: 11:24
 */

namespace App\Service\Dao;
use Im\Cloud\BroadcastRoomReq;

/**
 * Class Room
 * @package App\Service\Dao
 */
class Room
{
    /**
     * @param string $roomId
     * @param BroadcastRoomReq $broadcastRoomReq
     * @throws \Exception
     */
    public static function push(string $roomId,BroadcastRoomReq $broadcastRoomReq)
    {
        $pushData = $broadcastRoomReq->serializeToJsonString();
        $roomfds = bean(\App\Connection\Bucket::class)->roomfds($roomId);
        foreach ($roomfds as $fd){
            container()->get(Push::class)->push($fd,$pushData);
        }
    }

}