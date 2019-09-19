<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/12 0012
 * Time: 下午 2:56
 */

namespace App;

use App\Discovery;
use App\Event\Close;
use App\Event\Shutdown;
use Core\App;
use Core\Cloud;
use Core\Contract\ApplicationInterface;
use Core\Server\Server;
use Stdlib\Helper\Dir;
use Stdlib\Helper\Sys;
use Swoole\Process;

/**
 * Class Application
 * @package App
 */
class Application extends App implements ApplicationInterface
{
    /**
     * coroutine server processor
     * @var ApplicationInterface[]
     */
    protected $processor = [
        TcpServer::class,
        WebsocketServer::class
    ];

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
        foreach ($this->processor as $processor)
        {
            /** @var ApplicationInterface $server */
            $server = new $processor();
            go(function()use($server) {
                $server->handle();
            });
        }
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
            exit;
        });
    }
}