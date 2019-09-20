<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/12 0012
 * Time: 下午 2:56
 */

namespace Core;


use Core\Console\Cli;
use Core\Console\Console;
use Core\Container\Mapping\Bean;
use Core\Processor\AnnotationProcessor;
use Core\Processor\ConsoleProcessor;
use Core\Processor\Container;
use Core\Server\Helper\ServerHelper;
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
use Stdlib\Helper\Dir;
use Stdlib\Helper\Sys;
use Swoole\Coroutine;
use Swoole\Process;
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
    /** @
     * var string  */
    protected $pidFile = ROOT."/runtime/cloud.pid";
    /**
     * Record started server PIDs and with current workerId
     *
     * @var array
     */
    public static $pidMap = [
        'masterPid'  => 0,
        'managerPid' => 0,
        // if = 0, current is at master/manager process.
        'workerPid'  => 0,
        // if < 0, current is at master/manager process.
        'workerId'   => -1,
    ];
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
        $this->{$action}();
    }

    /**
     * @return bool
     */
    public function restart():bool
    {
        $this->stop();
        $this->start();
        return true;
    }

    /**
     * @return bool
     */
    public function start():bool
    {
        Cloud::server()->start();
        return true;
    }

    /**
     * @return bool
     */
    public function reload($onlyTaskWorker = false):bool
    {
        if(!$this->isRunning()){
            Console::writeln(sprintf('<error>server is not running now !</error>'));
            return false;
        }
        if (($pid = self::$pidMap['masterPid']) < 1) {
            Console::writeln(sprintf('<error>server is not running now !</error>'));
            return false;
        }

        // SIGUSR1(10):
        //  Send a signal to the management process that will smoothly restart all worker processes
        // SIGUSR2(12):
        //  Send a signal to the management process, only restart the task process
        $signal = $onlyTaskWorker ? 12 : 10;
        Console::writeln(sprintf('<success>server is reload now! send signal %s to pid:%s</success>', $signal, $pid));

        return ServerHelper::sendSignal($pid, $signal);
    }
    /**
     * @return bool
     */
    public function stop(): bool
    {
        if(!$this->isRunning()){
            Console::writeln(sprintf('<error>server is not running now !</error>'));
            return true;
        }

        $pid = $this->getPid();
        if ($pid < 1) {
            Console::writeln(sprintf('<error>server is not running now !</error>'));
            return false;
        }

        // SIGTERM = 15
        if (ServerHelper::killAndWait($pid, 15)) {
            Console::writeln(sprintf('<success>server is stop now! send signal %s to pid:%s</success>', 15, $pid));
            return ServerHelper::removePidFile(Cloud::$app->getPidFile());
        }

        return false;
    }
    /**
     * Check if the server is running
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        $pidFile = $this->getPidFile();

        // Is pid file exist ?
        if (file_exists($pidFile)) {
            // Get pid file content and parse the content
            $pidFile = file_get_contents($pidFile);

            // Parse and record PIDs
            [$masterPID, $managerPID] = explode(',', $pidFile);
            // Format type
            $masterPID  = (int)$masterPID;
            $managerPID = (int)$managerPID;

            self::$pidMap['masterPid']  = $masterPID;
            self::$pidMap['managerPid'] = $managerPID;
            return $masterPID > 0 && Process::kill($masterPID, 0);
        }

        return false;
    }
    /**
     * Set pid map
     *
     * @param
     */
    public function setPidMap(): void
    {
        $server = Cloud::swooleServer();
        if ($server->master_pid > 0) {
            self::$pidMap['masterPid'] = $server->master_pid;
        }

        if ($server->manager_pid > 0) {
            self::$pidMap['managerPid'] = $server->manager_pid;
        }
    }

    /**
     * create pid file => runtime/cloud.pid
     * @param int $master
     * @param int $manager
     */
    public function createPidFile(int $master,int $manager,$title = "")
    {

        $pidStr = sprintf('%s,%s', $master, $manager);
        $title  = $title ?? sprintf('php-%s master process (%s)', env("APP_NAME","im-nil-node"), ROOT);

        // Save PID to file
        $pidFile = $this->getPidFile();
        Dir::make(dirname($pidFile));
        file_put_contents($pidFile, $pidStr);
        // Set process title
        Sys::setProcessTitle($title);

    }
    /**
     * @param string $name
     *
     * @return int
     */
    public function getPid(string $name = 'masterPid'): int
    {
        return self::$pidMap[$name] ?? 0;
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
        $logger->setEnable(env("START_LOG",false));
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

    /**
     * @return mixed
     */
    public function getPidFile()
    {
        return $this->pidFile;
    }


}