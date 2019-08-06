<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/4
 * Time: 20:33
 */

namespace App\Process;
use App\Packet\Task;
use Core\Co;
use Log\Helper\Log;
use Process\Contract\AbstractProcess;
use Process\Process;

/**
 * Class BucketProcess
 * @package App\cloud\app\Process
 */
class TaskProcess extends AbstractProcess
{
    const Name = "im-cloud-task";
    public function __construct()
    {
        $this->name = self::Name;
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
        while (true){
            $body = $process->read();
            try{
                /** @var Task $task */
//                $task = container()->get(Task::class)->unpack($body);
                $task = (new Task())->unpack($body);
//                Co::create(function ()use($task){
                    $task->getClass()::{$task->getMethod()}(...$task->getArg());
//                },false);
            }catch (\Throwable $e)
            {
               Log::debug($e->getMessage());
            }
        }
    }
}