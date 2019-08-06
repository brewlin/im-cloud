<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/28
 * Time: 10:08
 */

namespace App\Service\Dao;


use ImRedis\Redis;
use Log\Helper\Log;

class Bucket
{
    const IpCounts =    "im-cloud-ip-counts";

    const BucketsRoom = "im-cloud-buckets-rooms";

    const RoomFds =     "im-cloud-roomid:%s";

    const KeyToFd =     "im-cloud-key:%s-to-fd";

    public static $ipCounts = [];
    public static $keyToFd = [];
    public static $bucketsRoom = [];
    public static $roomFds = [];

    /**
     * @param string $roomId
     * @param string $key
     * @param int $fd
     */
    public static function put(string $roomId = "",string $key,int $fd)
    {
        //ip ++
        self::$ipCounts[env("APP_HOST","127.0.0.1")] ++;
        //bind key to fd
        self::$keyToFd[$key] = $fd;
        if(empty($roomId)){
            return;
        }
        //check buckets exist roomid
        if(!isset(self::$bucketsRoom[$roomId])){
            self::$bucketsRoom[$roomId] = $roomId;
        }
        //add fd to room
        self::$roomFds[$roomId][$fd] = $fd;
    }

    /**
     * @param string $key
     */
    public static function del(string $roomId = "",string $key,int $fd)
    {
        //del key to fd
        unset(self::$keyToFd[$key]);
        if(!empty($roomId)){
            //del set room - fd
            unset(self::$roomFds[$roomId][$fd]);
        }
        //ip count --
        self::$ipCounts[env("APP_HOST","127.0.0.1")] --;
    }

    /**
     * @return array
     */
    public static function buckets()
    {
        return self::$bucketsRoom;
    }

    /**
     * @param string $roomId
     * @return array
     */
    public static function roomfds(string $roomId)
    {
        return self::$roomFds[$roomId];
    }

    /**
     * @param string $key
     * @return bool|mixed
     */
    public static function fd(string $key){
        $fd = self::$keyToFd[$key];
        if(empty($fd)){
            Log::error("key to fd :not find key:$key");
        }
        return $fd;

    }
}