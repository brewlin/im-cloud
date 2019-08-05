<?php

namespace Task;

use Core\App;
use Core\Container\Mapping\Bean;
use Stdlib\Helper\PhpHelper;
use Swoole\Coroutine;
use Task\Helper\TaskHelper;

/**
 * The task executor
 * @Bean()
 */
class TaskExecutor
{
    /**
     * @param string $data
     *
     * @return mixed
     */
    public function run(string $data)
    {
        $data = TaskHelper::unpack($data);

        $name   = $data['name'];
        $type   = $data['type'];
        $method = $data['method'];
        $params = $data['params'];
        $logid  = $data['logid'] ?? uniqid('', true);
        $spanid = $data['spanid'] ?? 0;

        if(!container()->has($name)){
            return false;
        }

//        list(, $coroutine) = $collector['task'][$name];
        $task = \bean($name);
        if ($type == Task::TYPE_CO) {
            $result = $this->runCoTask($task, $method, $params, $logid, $spanid, $name, $type);
        } else {
            $result = $this->runSyncTask($task, $method, $params, $logid, $spanid, $name, $type);
        }

        return $result;
    }

    /**
     * @param object $task
     * @param string $method
     * @param array  $params
     * @param string $logid
     * @param int    $spanid
     * @param string $name
     * @param string $type
     *
     * @return mixed
     */
    private function runSyncTask($task, string $method, array $params, string $logid, int $spanid, string $name, string $type)
    {
        $this->beforeTask($logid, $spanid, $name, $method, $type, get_parent_class($task));
        $result = PhpHelper::call([$task, $method], ...$params);
        $this->afterTask($type);

        return $result;
    }

    /**
     * @param object $task
     * @param string $method
     * @param array  $params
     * @param string $logid
     * @param int    $spanid
     * @param string $name
     * @param string $type
     *
     * @return bool
     */
    private function runCoTask($task, string $method, array $params, string $logid, int $spanid, string $name, string $type)
    {
        return Coroutine::create(function () use ($task, $method, $params, $logid, $spanid, $name, $type) {
            $this->beforeTask($logid, $spanid, $name, $method, $type, get_parent_class($task));
            PhpHelper::call([$task, $method], ...$params);
            $this->afterTask($type);
        });
    }

    /**
     * @param string $logid
     * @param int    $spanid
     * @param string $name
     * @param string $method
     * @param string $type
     * @param string $taskClass
     */
    private function beforeTask(string $logid, int $spanid, string $name, string $method, string $type, string $taskClass)
    {
//        $event = new BeforeTaskEvent(TaskEvent::BEFORE_TASK, $logid, $spanid, $name, $method, $type, $taskClass);
//        App::trigger($event);
    }

    /**
     * @param string $type
     */
    private function afterTask(string $type)
    {
        // Release system resources
//        App::trigger(AppEvent::RESOURCE_RELEASE);
//
//        App::trigger(TaskEvent::AFTER_TASK, null, $type);
    }
}