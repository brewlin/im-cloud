<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/15
 * Time: 23:09
 */

namespace App;
use App\Event\Close;
use App\Packet\Packet;
use App\Service\Service\Dispatcher;
use Core\Contract\ApplicationInterface;
use App\Connection\Bucket;
use App\Connection\Connection as Con;
use Core\Context\Context;
use Log\Helper\Log;
use Swoole\Coroutine\Http\Server;
use Swoole\Coroutine\Server\Connection;
use Core\Http\Request\Request as HttpRequest;
use Core\Http\Response\Response as HttpResponse;
use Core\Http\HttpDispatcher;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;

/**
 * Class WebsocketServer
 * @package App
 */
class HttpServer implements ApplicationInterface
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
     * @return void
     */
    public function handle():void
    {
        //start http&websocket server
        $server = new Server(env("HTTP_HOST"), env("HTTP_PORT"), false);
        $server->set([
            "open_http2_protocol" => true,
        ]);
        self::$httpServer = $server;
        //http request listen
        $server->handle(self::BaseUri,    [$this,"httpListen"]);
        $server->start();
    }

    /**
     * @param Request $request
     * @param $response
     */
    public function httpListen($request,$response)
    {
        $request = HttpRequest::new($request);
        Log::debug(json_encode(["get" => $request->query(),"post" => $request->post()]));
        $response = HttpResponse::new($response);
        HttpDispatcher::dispatch($request,$response);
    }

}