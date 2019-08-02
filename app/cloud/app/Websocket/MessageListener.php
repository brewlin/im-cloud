<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/26
 * Time: 21:31
 */

namespace App\Websocket;


use App\Lib\LogicClient;
use App\Packet\Packet;
use App\Packet\Protocol;
use App\Service\Dao\Bucket;
use App\Websocket\Exception\HandshakeException;
use App\Websocket\Exception\RequireArgException;
use Core\Context\Context;
use Core\Swoole\MessageInterface;
use Im\Logic\ConnectReply;
use Im\Logic\ConnectReq;
use Log\Helper\CLog;
use function Swlib\Http\str;
use Swoole\Websocket\Frame;
use Swoole\Websocket\Server;

/**
 * websocket 接收事件入口
 * Class MessageListener
 * @package App\Event
 */
class MessageListener implements MessageInterface
{
    const MisBody = "require data";
    /**
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server, Frame $frame): void
    {
        CLog::info("fd:{$frame->fd} data:{$frame->data}");
        try {
            /** @var Packet $packet */
            $packet = bean(Packet::class)->unpack($frame->data);
            $data = $packet->getBody();
            if (!$data)
                throw new \Exception(self::MisBody,0);
            Context::withValue("fd",$frame->fd);
            Context::withValue("body",$data);
            //dispatch
            container()->get(Dispatcher::class)
                       ->dispatch($packet);
        } catch (\Throwable $e) {
            $file = $e->getFile();
            $line = $e->getLine();
            $code = $e->getCode();
            $exception = $e->getMessage();
            $msg = $e->getMessage();
            $returnData = ['code' => $code,'msg' => $msg];
            CLog::error("file:".$file." line:$line code:$code msg:$exception");
//            $server->push($frame->fd,json_encode($returnData));
            $server->close($frame->fd);
        }
        Context::compelete();
    }

}