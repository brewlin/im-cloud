<?php

namespace Task;

use Core\App;
use Core\Swoole\PipeMessageInterface;
use Log\Helper\CLog;
use Log\Helper\Log;
use Task\Exception\TaskException;
use Task\Helper\TaskHelper;

/**
 * The task
 */
class Task
{
    /**
     * Coroutine task
     */
    const TYPE_CO = 'co';

    /**
     * Async task
     */
    const TYPE_ASYNC = 'async';

    /**
     * Deliver coroutine or async task
     *
     * @param string $taskName
     * @param string $methodName
     * @param array  $params
     * @param string $type
     * @param int    $timeout
     *
     * @return bool|array
     * @throws TaskException
     */
    public static function deliver(string $taskName, string $methodName, array $params = [], string $type = self::TYPE_CO)
    {

        $data   = TaskHelper::pack($taskName, $methodName, $params, $type);

        if(!App::isWorkerStatus() && !App::isCoContext()){
            return self::deliverByQueue($data);
        }

        if(!App::isWorkerStatus() && App::isCoContext()){
            throw new TaskException('Please deliver task by http!');
        }

        $server = App::server();
        // Deliver async task
        $tasres =  $server->task($data);
        Log::debug("task deliver: taskNmae:$taskName methodName:$methodName params:".json_encode($params)." res:".$tasres);
        return $tasres;
    }
    /**
     * Deliver coroutine or async task
     *
     * @param string $taskName
     * @param string $methodName
     * @param array  $params
     * @param string $type
     * @param int    $timeout
     *
     * @return bool|array
     * @throws TaskException
     */
    public static function Codeliver(string $taskName, string $methodName, array $params = [], string $type = self::TYPE_CO, $timeout = 3)
    {

        $data   = TaskHelper::pack($taskName, $methodName, $params, $type);

        if(!App::isWorkerStatus() && !App::isCoContext()){
            return self::deliverByQueue($data);
        }

        if(!App::isWorkerStatus() && App::isCoContext()){
            throw new TaskException('Please deliver task by http!');
        }

        $server = App::server();
        // Delier coroutine task

        $tasks[0]  = $data;
        $prifleKey = 'task' . '.' . $taskName . '.' . $methodName;
        $result = $server->taskCo($tasks, $timeout);

        return $result;
    }

    private static function deliverByQueue(string $data)
    {
        /* @var \Task\QueueTask $queueTask*/
        $queueTask = container()->get(QueueTask::class);
        return $queueTask->deliver($data);
    }

        /**
     * Deliver task by process
     *
     * @param string $taskName
     * @param string $methodName
     * @param array  $params
     * @param string $type
     * @param int    $timeout
     * @param int    $workId
     *
     * @return bool
     */
    public static function deliverByProcess(string $taskName, string $methodName, array $params = [], int $timeout = 3, int $workId = 0, string $type = self::TYPE_ASYNC): bool
    {
        /* @var PipeMessageInterface $pipeMessage */
        $server      = App::server();
        $pipeMessage = bean(PipeMessageInterface::class);
        $data = [
            'name'    => $taskName,
            'method'  => $methodName,
            'params'  => $params,
            'timeout' => $timeout,
            'type'    => $type,
        ];

        $message = $pipeMessage->pack(PipeMessageInterface::MESSAGE_TYPE_TASK, $data);
        return $server->sendMessage($message, $workId);
    }

    /**
     * Delivery multiple asynchronous tasks
     *
     * @param array $tasks
     *  <pre>
     *  $task = [
     *  'name'   => $taskName,
     *  'method' => $methodName,
     *  'params' => $params,
     *  'type'   => $type
     *  ];
     *  </pre>
     *
     * @return array
     */
    public static function async(array $tasks)
    {
        $server = App::server();

        $result = [];
        foreach ($tasks as $task) {
            if (!isset($task['type']) || !isset($task['name']) || !isset($task['method']) || !isset($task['params'])) {
                CLog::warning(sprintf('The task format of delivery is error，task=%s', json_encode($task, JSON_UNESCAPED_UNICODE)));
                continue;
            }

            $type = $task['type'];
            if ($type != self::TYPE_ASYNC) {
                CLog::warning(sprintf('Delivery is not an asynchronous task，task=%s', json_encode($task, JSON_UNESCAPED_UNICODE)));
                continue;
            }

            $data     = TaskHelper::pack($task['name'], $task['method'], $task['params'], $task['type']);
            $result[] = $server->task($data);
        }

        return $result;
    }

    /**
     * Delivery multiple co tasks
     *
     * @param array $tasks
     *  <pre>
     *  $tasks = [
     *  'name'   => $taskName,
     *  'method' => $methodName,
     *  'params' => $params,
     *  'type'   => $type
     *  ];
     *  </pre>
     *
     * @return array
     */
    public static function cor(array $tasks)
    {
        $server = App::server();

        $taskCos = [];
        foreach ($tasks as $task) {
            if (!isset($task['type']) || !isset($task['name']) || !isset($task['method']) || !isset($task['params'])) {
                Clog::warning(sprintf('The task format of delivery is error，task=%s', json_encode($task, JSON_UNESCAPED_UNICODE)));
                continue;
            }

            $type = $task['type'];
            if ($type != self::TYPE_CO) {
                CLog::warning(sprintf('Delivery is not a co task，task=%s', json_encode($task, JSON_UNESCAPED_UNICODE)));
                continue;
            }

            $taskCos[] = TaskHelper::pack($task['name'], $task['method'], $task['params'], $task['type']);
        }

        $result = [];
        if (!empty($taskCos)) {
            $result = $server->taskCo($tasks);
        }

        return $result;
    }

    /**
     * Run job by task
     *
     * @param string $taskName
     * @param string $methodName
     * @param array  $params
     *
     * @return bool
     */
    public static function run2(string $taskName, string $methodName, array $params)
    {
        if(!container()->has($taskName)) {
            CLog::error(sprintf('The %s task is not exist! ', $taskName));

            return false;
        }

        $task = bean($taskName);
        if (!method_exists($task, $methodName)) {
            CLog::error(sprintf('The %s job of %s task is not exist! ', $methodName, $taskName));

            return false;
        }

        $profileKey = $taskName . "-" . $methodName;
//        App::profileStart($profileKey);
        $result = $task->$methodName(...$params);

//        App::profileEnd($profileKey);

        return $result;
    }
}
