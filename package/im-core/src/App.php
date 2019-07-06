<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/12 0012
 * Time: 下午 2:56
 */

namespace Core;


use Core\Cloud;
use Core\Processor\AnnotationProcessor;
use Core\Processor\Container;
use Core\Server\HttpServer;
use Core\Log\Logger;
use Core\Processor\AppProcessor;
use Core\Processor\ConfigProcessor;
use Core\Processor\EnvProcessor;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use Goim\Comet\BroadcastReq;
use Goim\Comet\CometClient;
use Swoft\Log\Helper\CLog;

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
}