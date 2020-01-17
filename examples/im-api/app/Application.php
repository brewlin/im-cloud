<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/15 0012
 * Time: 下午 2:56
 */

namespace App;

use Core\App;
use Core\Contract\ApplicationInterface;
use Swoole\Process;

class Application extends App
{
    /**
     * coroutine server processor
     * @var ApplicationInterface[]
     */
    protected $processor = [
        HttpServer::class,
    ];

    /**
     * 取代 core 组件包的 start事件,自定义启动协程server
     * start the server
     */
    public function start():bool
    {
        $this->createPidFile(posix_getpid(),posix_getpid(),"im-api-s(".ROOT.")");
        foreach ($this->processor as $processor)
        {
            /** @var ApplicationInterface $server */
            $server = new $processor();
            go(function()use($server) {
                $server->handle();
            });
        }
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
        go([bean(Discovery::class),"run"]);
    }

    /**
     * signal to graceful shutdown
     */
    public function signal()
    {
        Process::signal(SIGTERM,function($signo){
            (new Shutdown())->shutdown();
            HttpServer::$httpServer->shutdown();
            //不知道为啥 协程server关闭不了，手动关闭连接后直接退出进程
            exit;
        });
    }
}