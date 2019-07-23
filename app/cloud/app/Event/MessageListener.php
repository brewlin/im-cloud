<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/26
 * Time: 21:31
 */

namespace App\Event;


use Core\Swoole\MessageInterface;
use Log\Helper\CLog;
use Swoole\Websocket\Frame;
use Swoole\Websocket\Server;

/**
 * websocket 接收事件入口
 * Class MessageListener
 * @package App\Event
 */
class MessageListener implements MessageInterface
{
    public function onMessage(Server $server, Frame $frame): void
    {
        try {
            $data = $frame->data;
            $data = json_decode($data, 1);
            if (!$data)
                return;
            //获取控制器
            $classname = 'App\\Websocket\\Controller\\' . $data['controller'];
            $action = $data['action'];
            if (class_exists($classname))
                (new $classname($data['content'], $frame->fd))->$action();
        } catch (\Throwable $e) {
            $file = $e->getFile();
            $line = $e->getLine();
            $code = $e->getCode();
            $exception = $e->getMessage();
            CLog::error("file:".$file." line:$line code:$code msg:$exception");
        }
    }

}