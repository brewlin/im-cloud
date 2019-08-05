<?php declare(strict_types=1);

namespace Core\Swoole;

use Swoole\Server;

/**
 * Interface TaskInterface
 *
 * @since 2.0
 */
interface TaskInterface
{
    /**
     * Task event
     *
     * @param Server $server
     * @param int    $taskId
     * @param int    $srcWorkerId
     * @param mixed  $data
     *
     * @return mixed
     */
    public function onTask(Server $server,  $taskId, int $srcWorkerId, $data);

    /**
     * @param Server $server
     * @param Server\Task $task
     * @return mixed
     */
    public function onCoTask(Server $server,  Server\Task $task);
}
