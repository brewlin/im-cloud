<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/23
 * Time: 18:30
 */

namespace App;


use App\Lib\CloudClient;
use App\Lib\LogicClient;
use Core\Cloud;
use Core\Processor\ProcessorInterface;
use Log\Helper\CLog;
use Log\Helper\Log;
use Process\Contract\AbstractProcess;
use Process\Process;
use Process\ProcessInterface;
use Swoole\Coroutine;

/**
 * 1.注册服务
 * 2.定时从服务中心发现服务 并刷新到本地serviceslist
 * Class DiscoveryProcess
 * @package App\Process
 */
class Discovery
{

    /**
     * @param Process $process
     */
    public function run()
    {
        Log::info("discvoery process start");
        //job节点无需注册 服务中心
        $config = config("discovery");
        $discoveryname = $config["consul"]["discovery"]["name"];
        while (true){
            $services = provider()->select()->getServiceList($discoveryname);
            if(empty($services)){
                Log::error("not find instance node:$discoveryname");
                CloudClient::updateService([]);
                goto SLEEP;
            }
            //独立子进程直接sleep阻塞即可
            CloudClient::updateService($services);
            SLEEP:
            Coroutine::sleep(5);
        }
    }
}