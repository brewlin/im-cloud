<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/4 0004
 * Time: 下坈 3:11
 */

namespace App\Service\Dao;


use App\Service\Model\Online;
use Core\Container\Mapping\Bean;
use ImRedis\Redis;

/**
 * Class RedisDao
 * @package App\Service\Dao
 * @Bean()
 */
class RedisDao
{
    const _prefixMidServer    = "mid_%d"; // mid -> key:server
	const _prefixKeyServer    = "key_%s"; // key -> server
	const _prefixServerOnline = "ol_%s";  // server -> online

    public function keyMidServer(int $mid)
    {
        return sprintf(self::_prefixMidServer, $mid);
    }

    public function keyKeyServer(string $key)
    {
        return sprintf(self::_prefixKeyServer, $key);
    }

    public function keyServerOnline(string $key)
    {
        return sprintf(self::_prefixServerOnline, $key);
    }


    /**
     * ServersByKeys get a server by key.
     * @param array $keys
     * @return array
     */
    public function getServersByKeys(array $keys)
    {
        $arg = [];
        foreach ($keys as $v){
            $arg[] = $this->keyKeyServer($v);
        }
        return Redis::mget($arg);
    }
    /**
     * @param array mids
     * @return array
     */
    public function getKeysByMids(array $mids)
    {
        $ress = [];
        foreach($mids as $mid){
            $ress = array_merge($ress,Redis::hgetall($this->keyMidServer($mid)));
        }
        return $ress;
    }
    /**
     * ServerOnline get a server online.
     * @return Online
     */
    public function serverOnline(string $server)
    {
        $key = $this->keyServerOnline($server);
        $online = new Online();

	    for($i = 0; $i < 64; $i++) {
            $ol = $this->_serverOnline($key,$i);
            if($ol){
                $online->server = $ol['server'];
                if($ol["updated"] > $online->updated) {
                    $online->updated = $ol["updated"];
                }
                foreach ($ol["roomCount"] as $room => $count) {
                    $online->roomCount[$room] = $count;
                }
            }
		}
		return $online;
	}

    /**
     * @param $key
     * @param $hashKey
     * @return array
     */
	public function _serverOnline($key ,$hashKey)
    {
       return Redis::hGet($key,$hashKey);
    }
}