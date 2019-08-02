<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/28
 * Time: 10:08
 */

namespace App\Service\Dao;


use ImRedis\Redis;
use Log\Helper\CLog;

class Bucket
{
    const IpCounts =    "im-cloud-ip-counts";

    const BucketsRoom = "im-cloud-buckets-rooms";

    const RoomFds =     "im-cloud-roomid:%s";

    const KeyToFd =     "im-cloud-key:%s-to-fd";

    /**
     * @param string $roomId
     * @param string $key
     * @param int $fd
     */
    public static function put(string $roomId = "",string $key,int $fd)
    {
        //ip ++
        Redis::hIncrBy(self::IpCounts,env("APP_HOST","127.0.0.1"),1);
        //bind key to fd
        Redis::set(sprintf(self::KeyToFd,$key),$fd);
        if(empty($roomId)){
            return;
        }
        //check buckets exist roomid
        if(!Redis::sIsMember(self::BucketsRoom,$roomId)){
            Redis::sAdd(self::BucketsRoom,$roomId);
        }
        //add fd to room
        Redis::sAdd(sprintf(self::RoomFds,$roomId),$fd);
    }

    /**
     * @param string $key
     */
    public static function del(string $roomId = "",string $key,int $fd)
    {
        //del key to fd
        Redis::del(sprintf(self::KeyToFd,$key));
        if(!empty($roomId)){
            //del set room - fd
            Redis::sRem(sprintf(self::RoomFds,$roomId),$fd);
        }
        //ip count --
        Redis::hIncrBy(self::IpCounts,env("APP_HOST","127.0.0.1"),-1);
    }

    /**
     * @return array
     */
    public static function buckets()
    {
        return Redis::sMembers(self::BucketsRoom);
    }

    /**
     * @param string $roomId
     * @return array
     */
    public static function roomfds(string $roomId)
    {
        return Redis::sMembers(sprintf(self::RoomFds,$roomId));
    }

    /**
     * @param string $key
     * @return bool|mixed
     */
    public static function fd(string $key){
        $fd = Redis::get(sprintf(self::KeyToFd,$key));
        if(empty($fd)){
            CLog::error("key to fd :not find key:$key");
        }
        return $fd;

    }
}