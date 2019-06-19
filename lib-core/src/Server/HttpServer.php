<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/15
 * Time: 12:00
 */

namespace Core\Server;

use Core\Listener\MessageListener;
use Core\Listener\ReceiveListener;
use Core\Listener\RequestListener;
use Core\Server\Server;
use Core\Swoole\SwooleEvent;
use Core\Server\TcpServer;

class HttpServer extends Server
{
    protected $port = 9090;
    protected $serverType = 'HTTP';
    protected $host = "127.0.0.1";
    public function __construct()
    {
        $this->port = env("HTTP_PORT");
        $this->host = env("HTTP_HOST");
        parent::__construct();
    }
    public function defaultSetting():array
    {
        return [
            'daemonize' => env("DAEMONIZE", 0),
            'worker_num' => (int)env("WORKER_NUM", 4),
            'open_http2_protocol' => true,
        ];
    }
    /**
     * start http server
     */
    public function start():void
    {
        $this->swooleServer = new \Swoole\Http\Server($this->host,$this->port,$this->mode,$this->type);
        $this->setListener();
        $this->startSwoole();
    }

    /**
     * set http & tcp on listener
     */
    public function setListener(): void
    {
        $this->httpListener = [
            SwooleEvent::MESSAGE => new MessageListener(),
            SwooleEvent::REQUEST => new RequestListener(),
        ];
        $this->listener = [
           "TCP" =>  new TcpServer()
        ];
    }

}