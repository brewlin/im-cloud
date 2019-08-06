<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/27
 * Time: 10:49
 */

namespace App\Lib;
use App\Service\Dao\Push;
use Core\Cloud;
use Core\Container\Mapping\Bean;
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
    public static function push(Proto $proto,int $op)
    {
        Log::info("Cloud broadcast op:$op data:".json_encode($proto));

        $start_fd = 0;
        while(true)
        {
            $conn_list = Cloud::server()->getSwooleServer()->getClientList($start_fd, 10);
            if ($conn_list===false or count($conn_list) === 0)
            {
                break;
            }
            $start_fd = end($conn_list);
            foreach($conn_list as $fd)
            {
                container()->get(Push::class)->push($fd,$proto->serializeToJsonString());
            }
        }

    }

}