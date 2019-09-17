<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/27
 * Time: 10:49
 */

namespace App\Service\Dao;
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
                container()->get(Push::class)->pushFd($fd,$op,$body);
            }
        }

    }


}