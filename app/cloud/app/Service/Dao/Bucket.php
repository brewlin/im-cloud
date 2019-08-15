<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/28
 * Time: 10:08
 */

namespace App\Service\Dao;


use App\cloud\app\Service\Service\Disconnect;
use App\Packet\Task;
use App\Process\TaskProcess;
use Core\Cloud;
use ImRedis\Redis;
use Log\Helper\Log;
use Process\ProcessManager;

class Bucket
{

    /**
     * @var array server => count
     */
    public static $ipCounts = [];
    /**
     * @var array roomid => roomid
     */
    public static $bucketsRoom = [];
    /**
     * @var array [
     *      roomid => [
     *              fd,fd fd fd
     *             ]
     * ]
     */
    public static $roomFds = [];
    /**
     * @var array key => fd
     */
    public static $keyToFd = [];
    /**
     * @var array fd => key
     */
    public static $FdToKey = [];
    /**
     * @var array fd => mid
     */
    public static $FdToMid = [];
    /**
     * @var array key => roomid
     */
    public static $keyToRoomId = [];

    /**
     * @param string $roomId
     * @param string $key
     * @param int $fd
     */
    public static function put(string $key,int $fd,string $mid,string $roomId = "")
    {
        //ipcount ++
        if(!isset(self::$ipCounts[env("APP_HOST","127.0.0.1")])){
            self::$ipCounts[env("APP_HOST","127.0.0.1")] = 0;
        }
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
        self::$keyToRoomId[$key] = $roomId;
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
            unset(self::$keyToRoomId[$key]);
        }
        //ip count --
        self::$ipCounts[env("APP_HOST","127.0.0.1")] --;
    }

    /**
     * 关闭连接销毁相关数据
     * @param int $fd
     */
    public static function disconnect(int $fd)
    {
        $key = self::$FdToKey[$fd];
        $mid = self::$FdToMid[$fd];
        if(empty($key)||empty($mid))return;
        $roomid = self::$keyToRoomId[$key];
        self::del($key,$fd,$roomid);
        /** @var Task $task */
        $sync = ["call" => [Disconnect::class,"disconnect"],"arg" => [$mid,$key]];
        Cloud::server()->getSwooleServer()->sendMessage($sync,rand(0,env("WORKER_NUM",4)-1));
//        ProcessManager::getProcesses(TaskProcess::Name)->write($task->pack());
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