<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/23
 * Time: 18:30
 */

namespace App;


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
 * 自定义进程
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
    public function run(Process $process)
    {
        $registerStatus = false;
        while(!$registerStatus){
            $registerStatus = provider()->select()->registerService();
            if(!$registerStatus){
                CLog::error("consul register false sleep 1 sec to reregiseter");
                Coroutine::sleep(1);
            }
        }
        $config = config("discovery");
        $discovery = $config["consul"]["discovery"]["name"];
        while (true){
            $services = provider()->select()->getServiceList($discovery);
            if(empty($services)){
                Log::error("not find any instance node:$discovery");
                LogicClient::updateService([]);
                goto SLEEP;
            }
            LogicClient::updateService($services);
SLEEP:
            Coroutine::sleep(5);
        }
    }
}