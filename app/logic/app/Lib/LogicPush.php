<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/1
 * Time: 23:05
 */

namespace App\logic\app\Lib;


use App\logic\app\Service\Dao\RedisDao;

class LogicPush
{
    public function pushKeys(int $op,string $keys,$msg)
    {
        $servers =  (new RedisDao())->keyKeyServer($keys);


    }

}