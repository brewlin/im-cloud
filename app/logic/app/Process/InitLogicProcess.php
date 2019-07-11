<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/23
 * Time: 18:30
 */

namespace App\Process;


use App\Lib\Logic;
use Core\Cloud;
use Core\Container\Mapping\Bean;
use Core\Processor\ProcessorInterface;
use Process\Contract\AbstractProcess;
use Process\Process;
use Process\ProcessInterface;

/**
 * @package App\Process
 * @Bean()
 */
class InitLogicProcess extends AbstractProcess
{
    public function __construct()
    {
        $this->name = "discovery";
    }

    public function check(): bool
    {
        return true;
    }

    /**
     * 自定义子进程 执行入口
     * @param Process $process
     */
    public function run(Process $process)
    {
        $scheduler = new Coroutine\Scheduler;
        $scheduler->add([Logic::class,"loadOnline"]);

        $scheduler->start();
    }

}