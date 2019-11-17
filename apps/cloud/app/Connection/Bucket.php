<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/18
 * Time: 22:34
 */

namespace App\Connection;
use App\Service\Service\Disconnect;
use Core\Container\Mapping\Bean;

/**
 * Class Bucket
 * @package App\Tcp
 * @Bean()
 */
class Bucket
{
    /**
     * @var Connection[]
     */
    private $conn = [];

    /**
     * @param Connection $conn
     */
    public function addConn(Connection $conn)
    {
        $this->conn[$conn->getFd()] = $conn;
    }

    /**
     * @param int $fd
     * @return bool|Connection
     */
    public function get(int $fd)
    {
        return $this->conn[$fd]??false;

    }

    /**
     * get all fds
     * @return array
     */
    public function getFdList():array
    {
        $list = [];
        foreach ($this->conn as $connection){
            $list[] = $connection->getFd();
        }
        return $list;
    }

    /**
     * @param Connection $conn
     */
    public function delConn(Connection $conn)
    {
        unset($this->conn[$conn->getFd()]);
    }

    /**
     * @param string $roomId
     * @param string $key
     * @param int $fd
     */
    public function push(string $key,int $fd,string $mid,string $roomId = "")
    {
        //bind key to fd
        \bean(Keys::class)->setFd($key,$fd);
        //set key
        $this->get($fd)->setKey($key);
        //set mid
        $this->get($fd)->setMid($mid);

        if(empty($roomId)){
            return;
        }
        \bean(Rooms::class)->add($roomId,$key,$fd);

    }

    /**
     * @param string $key
     */
    public function pop($key = "",$fd = null)
    {
        //del key to fd
        \bean(Keys::class)->delFd($key);

        //del set room - fd
        \bean(Rooms::class)->del($key,$fd);
        //ip count --
        \bean(Counts::class)->decr();
    }

    /**
     * 关闭连接销毁相关数据
     * @param int $fd
     */
    public function disconnect(int $fd)
    {
        $key = $this->get($fd)->getKey();
        $mid = $this->get($fd)->getMid();
        if(empty($key)||empty($mid))return;
        $this->pop($key,$fd);
        //grpc disconnect
        \bean(Disconnect::class)->disconnect($mid,$key);
    }

    /**
     * @return array
     */
    public function buckets()
    {
        return \bean(Rooms::class)->getRooms()??[];
    }

    /**
     * @param string $roomId
     * @return array
     */
    public function roomfds(string $roomId)
    {
        return \bean(Rooms::class)->getRoomFds($roomId)??[];
    }

    /**
     * @param string $key
     * @return bool|mixed
     */
    public function fd(string $key)
    {
        return \bean(Keys::class)->getFd($key)??false;
    }

    /**
     * @param int $fd
     * @return mixed
     */
    public function key(int $fd)
    {
        return $this->get($fd)->getKey();
    }

    /**
     * @param int $fd
     * @return mixed
     */
    public function mid(int $fd)
    {
        return $this->get($fd)->getMid();
    }

    /**
     * graceful close all connection
     */
    public function shutdown()
    {
        //foreach close and free all connection
        foreach ($this->conn as $connection){
            //grpc to logic clear the cache
            $this->disconnect($connection->getFd());
            //close the connection
            $this->pop($connection->getKey(),$connection->getFd());
            $this->delConn($connection);
            $connection->close();
        }
    }

}