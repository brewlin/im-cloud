<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/27
 * Time: 10:11
 */

namespace App\Service\Dao;


use Core\Cloud;
use Core\Container\Mapping\Bean;
use Log\Helper\CLog;

/**
 * Class Push
 * @package App\Lib
 * @Bean()
 */
class Push
{
    /**
     * @param int $fd
     * @param $data
     */
    public function push(int $fd,$data)
    {
        CLog::info("Cloud push:$fd  data:".json_encode($data));
        if(!($clientinfo = Cloud::server()->getSwooleServer()->getClientInfo($fd))){
            CLog::info("连接 fd:$fd 不存在,待发送数据:".json_encode($data));
           return;
        }
        //判断为websocket连接且已经握手完毕
        if(isset($clientinfo['websocket_status']) && $clientinfo['websocket_status'] == WEBSOCKET_STATUS_FRAME){
            Cloud::server()->getSwooleServer()->push($fd,$data);
            return;
        }
        Cloud::server()->getSwooleServer()->send($fd,$data);
    }

}