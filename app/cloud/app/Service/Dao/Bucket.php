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

    public static $ipCounts = [];
    public static $keyToFd = [];
    public static $FdToKey = [];
    public static $bucketsRoom = [];
    public static $roomFds = [];
    public static $FdToMid = [];

    /**
     * @param string $roomId
     * @param string $key
     * @param int $fd
     */
    public static function put(string $key,int $fd,string $mid,string $roomId = "")
    {
        //ip ++
        self::$ipCounts[env("APP_HOST","127.0.0.1")] ++;
        //bind key to fd
        self::$keyToFd[$key] = $fd;
        self::$FdToKey[$fd] = $key;
        self::$FdToMid[$fd] = $mid;
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
    public static function del(string $key,int $fd,string $roomId = "")
    {
        //del key to fd
        unset(self::$keyToFd[$key]);
        unset(self::$FdToKey[$fd]);
        unset(self::$FdToMid[$fd]);
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
        if(isset(self::$keyToFd[$key]))return self::$keyToFd[$key];
        return false;
    }

    /**
     * @param int $fd
     * @return mixed
     */
    public static function key(int $fd)
    {
        return self::$FdToKey[$fd];
    }

    /**
     * @param int $fd
     * @return mixed
     */
    public static function mid(int $fd)
    {
        return self::$FdToMid[$fd];
    }
}