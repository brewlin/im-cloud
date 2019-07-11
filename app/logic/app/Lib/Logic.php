<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/11
 * Time: 22:30
 */

namespace App\Lib;


use App\Service\Dao\RedisDao;

class Logic
{
    public static function loadOnline()
    {
        $server = LogicClient::$serviceList;
        foreach ($server as $ser){
            container()->get(RedisDao::class)->serverOnline($ser);
        }
    }

}