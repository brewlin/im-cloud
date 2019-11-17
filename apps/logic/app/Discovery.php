<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/23
 * Time: 18:30
 */

namespace App;


use App\Lib\CloudClient;
use Core\Container\Mapping\Bean;
use Log\Helper\CLog;
use Log\Helper\Log;
use Swoole\Coroutine;

/**
 * 1.注册服务
 * 2.定时从服务中心发现服务 并刷新到本地serviceslist
 * Class DiscoveryProcess
 * @package App\Process
 * @Bean()
 */
class Discovery
{
    /**
     * discovery
     */
    public function run()
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
            Coroutine::sleep(10);
        }
    }
}