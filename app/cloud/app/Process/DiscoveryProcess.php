<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/23
 * Time: 18:30
 */

namespace App\Process;


use App\Lib\LogicClient;
use Core\Cloud;
use Core\Processor\ProcessorInterface;
use Log\Helper\Log;
use Process\Contract\AbstractProcess;
use Process\Process;
use Process\ProcessInterface;

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
        $this->name = "im-cloud-discovery";
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
        while($registerStatus){
            $registerStatus = provider()->select()->registerService();
            sleep(1);
        }
        $config = config("discovery");
        $discovery = $config["consul"]["discovery"]["name"];
        while (true){
            $services = provider()->select()->getServiceList($discovery);
            if(empty($services)){
                Log::error("not find any instance node:$discovery");
                goto SLEEP;
            }
            for($i = 0; $i < (int)env("WORKER_NUM",4);$i++)
            {
                //将可以用的服务同步到所有的worker进程
                $sync = ["call" => [LogicClient::class,"updateService"],"arg" => [$services]];
                Cloud::server()->getSwooleServer()->sendMessage($sync,$i);
            }
SLEEP:
            sleep(10);
        }
    }
}