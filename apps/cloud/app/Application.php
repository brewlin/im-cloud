<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/12 0012
 * Time: 下午 2:56
 */

namespace App;

use Core\App;
use Core\Contract\ApplicationInterface;
use Swoole\Coroutine\Http\Server as HttpServer;
use Swoole\Coroutine\Server as TcpServer;
use Swoole\Coroutine\Server\Connection;
use Core\Http\Request\Request as HttpRequest;
use Core\Http\Response\Response as HttpResponse;
use Core\Http\HttpDispatcher;
/**
 * Class Application
 * @package App
 */
class Application extends App implements ApplicationInterface
{
    public function handle():void
    {
        //start http&websocket server
        go(function () {
            $server = new HttpServer(env("GRPC_HOST"), env("GRPC_PORT"), false);
            $server->set([
                "open_http2_protocol" => true,
            ]);
            $server->handle('/', function ($request, $response) {
                $request = HttpRequest::new($request);
                $response = HttpResponse::new($response);
                HttpDispatcher::dispatch($request,$response);
            });
            $server->start();
        });
        //start tcp server
        go(function () {
            $server = new TcpServer('0.0.0.0', 9601, false);
            $server->set([
                'package_max_length' => 1081920,
                'heartbeat_idle_time' => 20,
                'heartbeat_check_interval' => 5,
            ]);
            $server->handle(function (Connection $conn) use ($server) {
                while(true) {
                    $data = $conn->recv();

                }
            });
            $server->start();
        });
    }
}