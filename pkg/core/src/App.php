<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/12 0012
 * Time: 下午 2:56
 */

namespace Core;


use Core\Console\Cli;
use Core\Container\Mapping\Bean;
use Core\Processor\AnnotationProcessor;
use Core\Processor\ConsoleProcessor;
use Core\Processor\Container;
use Core\Server\HttpServer;
use Core\Processor\AppProcessor;
use Core\Processor\ConfigProcessor;
use Core\Processor\EnvProcessor;
use Core\Server\WebsocketServer;
use Log\CLogger;
use Log\Handler\FileHandler;
use Log\Helper\CLog;
use Log\Logger;
use Monolog\Formatter\LineFormatter;
use Swoole\Coroutine;
use Swoole\Server;

class App
{
    /**
     * env file path
     * @var string
     */
    protected $envFile = ROOT."/.env";
    /**
     * runtime/log/notice.log
     * @var string
     */
    protected $noticeFile = ROOT."/runtime/logs/notice.log";
    /**
     * runtime/log/error.log
     * @var string
     */
    protected $errorFile = ROOT."/runtime/logs/error.log";
    /**
     * @var Processor\Processor
     */
    private $processor;

    const FONT_LOGO = "
 _                  _                 _                           _      
(_)_ __ ___     ___| | ___  _   _  __| |  _____      _____   ___ | | ___ 
| | '_ ` _ \   / __| |/ _ \| | | |/ _` | / __\ \ /\ / / _ \ / _ \| |/ _ \
| | | | | | | | (__| | (_) | |_| | (_| | \__ \\ V  V / (_) | (_) | |  __/
|_|_| |_| |_|  \___|_|\___/ \__,_|\__,_| |___/ \_/\_/ \___/ \___/|_|\___|
";
    public function __construct()
    {
        Cloud::$app = $this;
        //initLog
        $config = [
            'name'    => 'im-cloud',
            'enable'  => true,
            'output'  => true,
            'levels'  => [],
            'logFile' => ''
        ];
        CLog::init($config);
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
            new ConsoleProcessor($this),
            new AnnotationProcessor($this),
            new Container($this),
        ];
    }

    /**
     * start run
     */
    public function run(){
        $this->processor->handle();
        $this->initLog();
        $this->serverHandle();
    }

    /**
     * start reload stop
     */
    public function serverHandle()
    {
        $action = env("APP","start");
        if(env("ENABLE_WS")){
            ( new WebsocketServer());
        }else{
            (new HttpServer());
        }
        Cloud::server()->{$action}();
    }

    /**
     * env path
     * @return string
     */
    public function getEnvFile():string {
       return $this->envFile;
    }
    public function initLog()
    {
//        $format = '%datetime% [%level_name%] [%channel%] [%event%] [tid:%tid%] [cid:%cid%] [traceid:%traceid%] [spanid:%spanid%] [parentid:%parentid%] %messages%';
        $format = '%datetime% [%level_name%] [%channel%] %messages%';
        $dateFormat = 'Y-m-d H:i:s';
        $lineFormatter = new LineFormatter($format,$dateFormat);
        $noticeHandler = new FileHandler();
        $noticeHandler->setFormatter($lineFormatter);
        $noticeHandler->setLevels([
            Logger::NOTICE,
            Logger::INFO,
            Logger::DEBUG,
            Logger::TRACE,
        ]);
        $noticeHandler->setLogfile($this->noticeFile);
        $noticeHandler->init();

        $appHandler = new FileHandler();
        $appHandler->setFormatter($lineFormatter);
        $appHandler->setLevels([
            Logger::ERROR,
            Logger::WARNING,
        ]);
        $appHandler->setLogfile($this->errorFile);
        $appHandler->init();
        /** @var \Log\Logger $logger */
        $logger = \bean(\Log\Logger::class);
        $logger->setEnable(env("START_LOG",true));
        $logger->setHandlers([$noticeHandler,$appHandler]);

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