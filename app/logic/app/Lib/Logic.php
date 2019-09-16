<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/11
 * Time: 22:30
 */

namespace App\Lib;


use App\logic\app\Event\WorkerStartListener;
use App\Service\Dao\RedisDao;
use App\Service\Model\Online;
use Core\App;
use Core\Cloud;
use Swoole\Coroutine;

class Logic
{
    /**
     * @var Online
     */
    public static $roomCount;
    public static function loadOnline()
    {
        //放到协程调度运行
        while (true){
            $server = LogicClient::$table->getKeys();
            if(empty($server))goto SLEEP;
            foreach ($server as $ser){
                /**
                 * @var Online
                 */
                $online = container()->get(RedisDao::class)->serverOnline($ser);
            }
//            $pid = Cloud::server()->getSwooleServer()->worker_pid;
//            for($i = 0;$i < env("WORKER_NUM",4);$i ++){
//                if($pid != WorkerStartListener::INIT_LOGIC){
//                    Cloud::server()->getSwooleServer()->sendMessage(["call" => [Logic::class,"updateOnline"],"arg" => [$online->roomCount]],$i);
//                }
                //当前进程则直接更新
                self::$roomCount = $online->roomCount;
//            }
SLEEP:
            Coroutine::sleep(10);
        }
    }

    /**
     * 每个进程同步数据
     * @param $roomCount
     */
    public static function updateOnline($roomCount){
        self::$roomCount = $roomCount;
    }

}