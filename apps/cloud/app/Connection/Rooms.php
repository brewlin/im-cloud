<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/19 0019
 * Time: 下午 3:51
 */

namespace App\Connection;

use Core\Container\Mapping\Bean;

/**
 * Class Rooms
 * @package App\Connection
 * @Bean()
 */
class Rooms
{
    /**
     * all roomids
     * @var array
     */
    private $rooms = [];

    /**
     * key to roomid
     * @var array
     */
    private $keys = [];

    /**
     * @var array [
     *      roomid => [
     *              fd,fd fd fd
     *             ]
     * ]
     */
    private $fds = [];

    /**
     * @return array
     */
    public function getRooms(): array
    {
        return $this->rooms??[];
    }

    /**
     * @param $roomId
     * @return array
     */
    public function getRoomFds($roomId):array
    {
        return $this->fds[$roomId]??[];

    }

    /**
     * @param string $roomId
     * @param string $key
     * @param int $fd
     */
    public function add(string $roomId,string $key,int $fd)
    {
        //check buckets exist roomid
        if(!isset($this->rooms[$roomId]))
            $this->rooms[$roomId] = $roomId;
        //add key to room
        $this->keys[$key] = $roomId;
        //add fd to room
        $this->fds[$roomId][$fd] = $fd;
    }
    /**
     * @param string $roomId
     * @param string $key
     * @param int $fd
     */
    public function del(string $key,int $fd)
    {
        $roomId = $this->keys[$key];
        if(empty($roomId))return;
        //del key to room
        unset($this->keys[$key]);
        //del fd to room
        unset($this->fds[$roomId][$fd]);
    }

}