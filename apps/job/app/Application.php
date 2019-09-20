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
use Core\Cloud;
use Core\Co;
use Core\Console\Console;
use Core\Contract\ApplicationInterface;
use Core\Http\HttpDispatcher;
use Log\Helper\CLog;
use Log\Helper\Log;
use Stdlib\Helper\Dir;
use Stdlib\Helper\Sys;
use Swoole\Coroutine\Http\Server;
use Core\Http\Request\Request as HttpRequest;
use Core\Http\Response\Response as HttpResponse;

/**
 * Class Application
 * @package App
 */
class Application extends App implements ApplicationInterface
{
    /**
     * start the tcp and websocket server
     */
    public function handle(): void
    {
        $action = env("APP", "start");
        $this->{$action}();
    }
    /**
     * start the server
     */
    public function start():void
    {
        go(function (){
            try{
                consumer()->consume(new Consumer());
            }catch (\Throwable $e){
                Log::error($e->getMessage());
                Console::write("<error>{$e->getMessage()}</error>");
            }
        });
        $this->setPidMap();
        $this->process();
        //注册信号
        $this->signal();
    }
    public function setPidMap()
    {
        $pidStr = sprintf('%s,%s', posix_getpid(), posix_getpid());
        $title  = sprintf('cloud-s (%s)',ROOT);

        // Save PID to file
        $pidFile = Cloud::$app->getPidFile();
        Dir::make(dirname($pidFile));
        file_put_contents($pidFile, $pidStr);
        // Set process title
        Sys::setProcessTitle($title);
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
            (new Shutdown())->shutdown();
            TcpServer::$tcpServer->shutdown();
            WebsocketServer::$httpServer->shutdown();
            exit;
        });
    }
}