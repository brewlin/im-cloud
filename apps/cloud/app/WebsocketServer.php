<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/18
 * Time: 23:09
 */

namespace App;
use App\Packet\Packet;
use App\Service\Service\Dispatcher;
use Core\Contract\ApplicationInterface;
use App\Server\Bucket;
use App\Server\Connection as Con;
use Core\Context\Context;
use Log\Helper\Log;
use Swoole\Coroutine\Http\Server;
use Swoole\Coroutine\Server\Connection;
use Core\Http\Request\Request as HttpRequest;
use Core\Http\Response\Response as HttpResponse;
use Core\Http\HttpDispatcher;
use Swoole\WebSocket\Frame;

/**
 * Class WebsocketServer
 * @package App
 */
class WebsocketServer implements ApplicationInterface
{
    /**
     * @var Server
     */
    public static $httpServer;
    /**
     * @var string
     */
    const BaseUri = "/";
    /**
     * @var string
     */
    const WsPath = "/sub";

    /**
     * @return void
     */
    public function handle():void
    {
        //start http&websocket server
        $server = new Server(env("GRPC_HOST"), env("GRPC_PORT"), false);
        $server->set([
            "open_http2_protocol" => true,
        ]);
        self::$httpServer = $server;
        //http request listen
        $server->handle(self::BaseUri,    [$this,"httpListen"]);
        //websocket conn listen
        $server->handle(self::WsPath, [$this,"wsListen"]);
        $server->start();
    }

    /**
     * @param $request
     * @param $response
     */
    public function httpListen($request,$response)
    {
        $request = HttpRequest::new($request);
        $response = HttpResponse::new($response);
        HttpDispatcher::dispatch($request,$response);
    }

    /**
     * @param $request
     * @param $ws
     */
    public function wsListen($request,$ws)
    {
        //upgrade ws
        $conn = new Con($ws,Con::Websocket);
        $conn->upgrade();
        bean(Bucket::class)->put($conn);
        $this->eventLoop($conn);
        $conn->close();
        bean(Bucket::class)->del($conn);
    }

    /**
     * @param Con $conn
     */
    public function eventLoop(Con $conn){
        while(true) {
            $data = $conn->recv();
            if($data == '' || $data === false ){
                //disconnect
                break;
            }
            try {
                Log::info("fd:{$conn->getFd()} data:{$data->data}");
                $this->dispatch($data);
            } catch (\Throwable $e) {
                $file = $e->getFile();
                $line = $e->getLine();
                $code = $e->getCode();
                $exception = $e->getMessage();
                Log::error("file:" . $file . " line:$line code:$code msg:$exception");

                //wsclient 未提供close函数，直接break loop返回就可以了
                break;
            }
            //destory context
            Context::compelete();
        }
    }

    /**
     * token check '{"mid":123, "room_id":"live://1000", "platform":"web", "accepts":[1000,1001,1002]}'
     * @param Frame $frame
     * @throws \Exception
     */
    public function dispatch(Frame $frame)
    {
        /** @var Packet $packet */
        $packet = bean(Packet::class)->unpack($frame->data);
        Context::withValue(Packet::class,$packet);
        Context::withValue("fd",$frame->fd);

        //dispatch
        container()->get(Dispatcher::class)
                   ->dispatch();
    }

}