<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/4 0004
 * Time: 下午 3:11
 */

namespace App\Service\Dao;


use ImRedis\Redis;

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
}