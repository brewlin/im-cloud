<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/18
 * Time: 11:04
 */
namespace Process;


use Process\Contract\AbstractProcess;
use Log\Helper\CLog;
use Swoole\Server;

class ProcessManager
{
    /**
     * @var array
     */
    protected static $processes = [];
    /**
     * @var array[Process]
     */
    protected static $processPool = [];

    /**
     * register process to pool
     * @param string $name
     * @param ProcessInterface $process
     * @throws \Exception()
     */
    public static function register(string $name ,ProcessInterface $process): void
    {
//        try
//        {
            static::$processes[$name] = $process;
            static::$processPool[$name] = ProcessBuilder::create($name);
//        }catch (\Throwable $e){
//            CLog::error("process register false :process name:$name
//            \n{$e->getLine()}
//            ");

//        }
    }

    /**
     * @param string $name
     * @return bool|\Swoole\Process
     */
    public static function getProcesses(string $name)
    {
        if(!isset(self::$processPool[$name])){
            CLog::error("proessname $name isn't exist");
            return false;
        }
        /** @var Process $process */
        $process = self::$processPool[$name];
        return $process->getProcess();
    }

    /**
     * @param string $processName
     * @throws \Exception
     * @return AbstractProcess
     */
    public static function get(string $processName):AbstractProcess{
        if(!isset(static::$processes[$processName])){
            CLog::error("process name: $processName  isn't exist");
            throw new \Exception("process name: $processName  isn't exist");
        }
        return static::$processes[$processName];
    }

    /**
     * @return array
     */
    public static function all(): array
    {
        return static::$processes;
    }

    /**
     * @return void
     */
    public static function clear(): void
    {
        static::$processes = [];
    }

    /**
     * load the process
     * @param Server $server
     */
    public static function load(Server $server){
       foreach (self::$processPool as $name => $process){
           $server->addProcess($process->getProcess());
       }
    }
}
