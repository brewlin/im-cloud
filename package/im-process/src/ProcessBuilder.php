<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/18
 * Time: 11:04
 */
namespace Process;

use Stdlib\Helper\PhpHelper;
use Swoole\Process as SwooleProcess;


/**
 * The process builder
 */
class ProcessBuilder
{
    /**
     * @var array
     */
    private static $processes = [];

    /**
     * create child process by soole start
     * @param string $name
     *
     * @return Process
     */
    public static function create(string $name): Process
    {
        if (isset(self::$processes[$name])) {
            return self::$processes[$name];
        }

        list($name, $boot, $pipe, $inout, $co) = self::getProcessMaping($name);
        $swooleProcess = new SwooleProcess(function (SwooleProcess $swooleProcess) use ($name, $co, $boot) {
            //start the process
            $process = new Process($swooleProcess);
            self::runProcessByDefault($name, $process, $boot);
        }, $inout, $pipe,$co);
        $process = new Process($swooleProcess);
        self::$processes[$name] = $process;

        return $process;
    }

    /**
     * @param string $name
     *
     * @return Process
     * @throws \Exception
     */
    public static function get(string $name): Process
    {
        if (!isset(self::$processes[$name])) {
            throw new \Exception(sprintf('The %s process is not create, you must to create by first !', $name));
        }

        return self::$processes[$name];
    }

    /**
     * @param string $name
     *
     * @return array
     * @throws \Exception
     */
    private static function getProcessMaping(string $name): array
    {
        $process = ProcessManager::get($name);
        $data = [
            $process->name,
            $process->boot,
            $process->pipe,
            $process->inout,
            $process->co,
        ];

        return $data;
    }

    /**
     * @param string  $name
     * @param Process $process
     * @param bool    $boot
     */
    private static function runProcessByDefault(string $name, Process $process, bool $boot)
    {
        /* @var ProcessInterface $processObject */
        $processObject = ProcessManager::get($name);
        self::beforeProcess($name, $boot);
        if($processObject->check()){
            PhpHelper::call([$processObject, 'run'], $process);
        }
        self::afterProcess();
    }

    /**
     * After process
     *
     * @param string $processName
     * @param bool   $boot
     */
    private static function beforeProcess(string $processName, $boot)
    {

        self::waitChildProcess($processName, $boot);
    }


    /**
     * After process
     */
    private static function afterProcess()
    {
    }

    /**
     * Wait child process
     */
    private static function waitChildProcess(string $name, $boot)
    {
        /* @var ProcessInterface $processObject */
        $processObject = ProcessManager::get($name);
        if (($hasWait = method_exists($processObject, 'wait')) || $boot) {
            Process::signal(SIGCHLD, function($sig) use ($name, $processObject, $hasWait) {
                while($ret =  Process::wait(false)) {
                    if ($hasWait) {
                        $processObject->wait($ret);
                    }

                    unset(self::$processes[$name]);
                }
            }); 
        }
    }
}
