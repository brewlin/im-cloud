<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/12 0012
 * Time: 下午 2:56
 */

namespace App;

use Core\App;
use App\Consumer\Consumer;
use Core\Console\Console;
use Log\Helper\Log;
use Swoole\Process;

/**
 * Class Application
 * @package App
 */
class Application extends App
{
    /**
     * 取代 core 组件包的 start事件,自定义启动协程server
     * start the server
     */
    public function start():bool
    {
        //create pid file
        $this->createPidFile(posix_getpid(),posix_getpid(),"job-s(".ROOT.")");

        //start coroutine server
        go(function (){
            try{
                consumer()->consume(new Consumer());
            }catch (\Throwable $e){
                Log::error($e->getMessage());
                Console::write("<error>{$e->getMessage()}</error>");
            }
        });
        $this->process();
        //注册信号
        $this->signal();
        return true;

    }

    /**
     * coroutiner long runner
     */
    public function process()
    {
        //discovery consul..
        go([new Discovery(),"run"]);
    }

    /**
     * signal to graceful shutdown
     */
    public function signal()
    {
        Process::signal(SIGTERM,function($signo){
            //close the connection
            exit;
        });
    }
}