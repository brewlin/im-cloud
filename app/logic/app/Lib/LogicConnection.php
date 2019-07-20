<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/19 0019
 * Time: 下午 4:23
 */

namespace App\Lib;


use App\Service\Dao\RedisDao;
use App\Service\Model\Online;
use Core\Container\Mapping\Bean;

/**
 * Class LogicConnection
 * @package App\Lib
 * @Bean()
 */
class LogicConnection
{
    /**
     * @param string $server
     * @param string $cookie
     * @param $token
     * @return array
     * @throws \Exception
     */
    public function connection(string $server,string $cookie,$token)
    {

        $tokenKey = ['mid','key','room_id','platform','accepts'];
        foreach ($tokenKey as $k){
            if(!isset($token[$k])){
                return;
            }
        }
        $mid = $token['mid'];
        $roomID = $token["room_id"];
        $accepts = $token["accepts"];
        $key = $token["key"];
        if(empty($key)){
            mt_srand();
            $key = md5(uniqid(mt_rand()));
        }
        container()->get(RedisDao::class)
                    ->addMapping($mid,$key,$server);
        return compact("mid","key","roomID","accepts");

    }

    /**
     * @param int $mid
     * @param string $key
     * @param string $server
     * @throws \Exception
     */
    public function disConnection(int $mid,string $key,string $server)
    {
        container()->get(RedisDao::class)
                    ->delMapping($mid,$key,$server);

    }

    /**
     * @param int $mid
     * @param string $key
     * @param string $server
     * @throws \Exception
     */
    public function heartBeat(int $mid,string $key,string $server)
    {
        container()->get(RedisDao::class)
                    ->expireMapping($mid,$key);
    }

    /**
     * @param string $server
     * @param array $roomCount
     * @return Online
     */
    public function renewOnline(string $server,array $roomCount)
    {
        $online = new Online();
        $online->server = $server;
        $online->roomCount = $roomCount;
        $online->updated = time();
        \bean(RedisDao::class)->addServerOnline($server,$online);
        return Logic::$roomCount;
    }

}