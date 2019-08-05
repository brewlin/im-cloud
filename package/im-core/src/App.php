<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/12 0012
 * Time: 下午 2:56
 */

namespace Core;


use Core\Processor\AnnotationProcessor;
use Core\Processor\Container;
use Core\Server\HttpServer;
use Core\Log\Logger;
use Core\Processor\AppProcessor;
use Core\Processor\ConfigProcessor;
use Core\Processor\EnvProcessor;
use Core\Server\WebsocketServer;
use Log\Helper\CLog;
use Swoole\Coroutine;
use Swoole\Server;

class App
{
    /**
     * env file path
     * @var string
     */
    protected $envFile = '';
    /**
     * @var Processor\Processor
     */
    private $processor;

    public function __construct()
    {
        Cloud::$app = $this;
        //initLog
        Logger::initLog();
        CLog::info('Swoole\Runtime::enableCoroutine');
        //set env path
        $this->setEnvFile();
        //run handle
        $processors = $this->processors();
        $this->processor = new AppProcessor($this);
        $this->processor->addFirstProcessor(...$processors);
    }

    /**
     * init operation
     * @return array
     */
    public function processors():array{
        return [
            new EnvProcessor($this),
            new ConfigProcessor($this),
            new AnnotationProcessor($this),
            new Container($this),
        ];
    }

    /**
     * start run
     */
    public function run(){
        $this->processor->handle();
        if(env("ENABLE_WS")){
            ( new WebsocketServer())->start();
        }
        (new HttpServer())->start();
    }

    /**
     * set env file path
     */
    public function setEnvFile(){
        $this->envFile = ROOT."/.env";
    }

    /**
     * env path
     * @return string
     */
    public function getEnvFile():string {
       return $this->envFile;
    }

    /**
     * @return bool 当前是否是worker状态
     */
    public static function isWorkerStatus(): bool
    {
        if(Cloud::server())
        if (Cloud::server() === null) {
            return false;
        }

        $server = Cloud::server()->getSwooleServer();

        if ($server && \property_exists($server, 'taskworker') && ($server->taskworker === false)) {
            return true;
        }

        return false;
    }

    /**
     * Get workerId
     */
    public static function getWorkerId(): int
    {
        if (Cloud::server() === null) {
            return 0;
        }

        $server = Cloud::server()->getSwooleServer();

        if ($server && \property_exists($server, 'worker_id') && $server->worker_id > 0) {
            return $server->worker_id;
        }

        return 0;
    }

    /**
     * Whether it is coroutine context
     *
     * @return bool
     */
    public static function isCoContext(): bool
    {
        return Coroutine::getuid() >0;
    }

    /**
     * @return Server
     */
    public static function server():Server
    {
        return Cloud::server()->getSwooleServer();
    }
}