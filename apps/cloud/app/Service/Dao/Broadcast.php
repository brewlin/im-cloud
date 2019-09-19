<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/27
 * Time: 10:49
 */

namespace App\Service\Dao;
use App\Connection\Connection;
use Core\Cloud;
use Im\Cloud\Proto;
use Log\Helper\Log;

/**
 * Class Broadcast
 * @package App\Lib
 */
class Broadcast
{
    /**
     * @param Proto $proto
     * @param int $op
     */
    public static function push(int $op,$body)
    {
        Log::info("Cloud broadcast op:$op data:".json_encode($body));
        go(function ()use($op,$body){
            /** @var Connection[] $conns */
            $conns = bean(\App\Connection\Bucket::class)->getFdList();
            foreach ($conns as $fd){
                container()->get(Push::class)->pushFd($fd,$op,$body);
            }

        });

    }


}