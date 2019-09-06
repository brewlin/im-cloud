<?php

namespace Task\Listeners;

use Core\App;
use Core\Container\Mapping\Bean;
use Core\Swoole\FinishInterface;
use Core\Swoole\TaskInterface;
use Log\Helper\CLog;
use Log\Helper\Log;
use Task\TaskExecutor;
use Swoole\Server;

/**
 * The listener of swoole task
 * @Bean()
 */
class TaskEventListener implements TaskInterface,FinishInterface
{
    public function onFinish(Server $server, int $taskId, string $data):void
    {
//        App::trigger(TaskEvent::FINISH_TASK, $taskId, $data);
    }

    /**
     * @param \Swoole\Server $server
     * @param int            $taskId
     * @param int            $workerId
     * @param mixed          $data
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function onTask(Server $server, $taskId, int $workerId, $data)
    {
        Log::debug("ontask : taskId:$taskId data:$data");
        try {
            /* @var TaskExecutor $taskExecutor*/
            $taskExecutor = bean(TaskExecutor::class);
            $result = $taskExecutor->run($data);
        } catch (\Throwable $throwable) {
            CLog::error(sprintf('TaskExecutor->run %s file=%s line=%d ', $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()));
            $result = false;

            // Release system resources
//            App::trigger(AppEvent::RESOURCE_RELEASE);
//            App::trigger(TaskEvent::AFTER_TASK);
        }
        return $result;
    }

    /**
     * @param Server $server
     * @param Server\Task $task
     * @return bool|mixed
     */
    public function onCoTask(Server $server, Server\Task $task)
    {
        Log::debug("oncotask : taskId:$task->id data:$task->data");
        try {
            /* @var TaskExecutor $taskExecutor*/
            $taskExecutor = bean(TaskExecutor::class);
            $result = $taskExecutor->run($task->data);
        } catch (\Throwable $throwable) {
            CLog::error(sprintf('TaskExecutor->run %s file=%s line=%d ', $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()));
            $result = false;

            // Release system resources
//            App::trigger(AppEvent::RESOURCE_RELEASE);
//            App::trigger(TaskEvent::AFTER_TASK);
        }
        return $result;
    }
}