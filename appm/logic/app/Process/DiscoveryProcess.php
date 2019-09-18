<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/23
 * Time: 18:30
 */

namespace App\Process;


use App\Lib\CloudClient;
use Log\Helper\CLog;
use Log\Helper\Log;
use Process\Contract\AbstractProcess;
use Process\Process;

/**
 * 自定义进程
 * 1.注册服务
 * 2.定时从服务中心发现服务 并刷新到本地serviceslist
 * Class DiscoveryProcess
 * @package App\Process
 */
class DiscoveryProcess extends AbstractProcess
{
    public function __construct()
    {
        $this->name = "im-logic-discovery";
    }

    public function check(): bool
    {
        return true;
    }

    /**
     * 自定义子进程 执行入口
     * @param Process $process
     */
    public function run(Process $process)
    {
        $registerStatus = false;
        //注册失败则一直重试注册到发现中心
        while(!$registerStatus){
            $registerStatus = provider()->select()->registerService();
            if(!$registerStatus){
                sleep(1);
                CLog::error("consul register false sleep 1 sec to reregiseter");
            }
        }
        while (true){
            $services = provider()->select()->getServiceList("grpc-im-cloud-node");
            if(empty($services)){
                Log::error("not find any instance node:grpc-im-cloud-node");
                CloudClient::updateService([]);
                goto SLEEP;
            }
            CloudClient::updateService($services);
SLEEP:
            sleep(10);
        }
    }
}