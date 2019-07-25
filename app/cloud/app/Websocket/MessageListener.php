<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/26
 * Time: 21:31
 */

namespace App\Websocket;


use App\Websocket\Exception\HandshakeException;
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
    /**
     * @param Server $server
     * @param Frame $frame
     * token check '{"mid":123, "room_id":"live://1000", "platform":"web", "accepts":[1000,1001,1002]}'
     */
    public function onMessage(Server $server, Frame $frame): void
    {
        try {
            $data = $frame->data;
            $data = json_decode($data, 1);
            if (!$data)
                throw new HandshakeException("require token",0);
            //hanshake
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
            $msg = $e->getMessage();
            $returnData = ['code' => $code,'msg' => $msg];
            CLog::error("file:".$file." line:$line code:$code msg:$exception");
            $server->push($frame->fd,$returnData);
            $server->close($frame->fd);
        }
    }
    public function handshake(Frame $frame){

        throw new \Exception("url unauthorized",0);

    }

}