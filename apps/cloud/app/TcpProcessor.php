<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/18
 * Time: 23:06
 */

namespace App;
use Core\Contract\ApplicationInterface;

/**
 * Class TcpServer
 * @package App
 */
class TcpProcessor implements ApplicationInterface
{
    public function handle(): void
    {
        // TODO: Implement handle() method.
    }
    /**
     * @var TcpServer\
     */
    public static $tcpServer;
    /**
     * @var HttpServer
     */
    public static $httpServer;

    /**
     * @return void
     */
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
            /** @var Server $server */
            $server = new TcpProcessor(env("TCP_HOST","0.0.0.0"), env("TCP_PORT",9501), false);
            $server->set([
                'package_max_length' => 1081920,
                'heartbeat_idle_time' => 20,
                'heartbeat_check_interval' => 5,
            ]);
            self::$tcpServer = $server;
            $server->handle([$this,"tcpAccept"]);
            $server->start();
        });
    }
    public function tcpAceept(Connection $conn)
    {
        bean(Bucket::class)->put($conn);
        $this->eventLoop($conn);
        $conn->close();
        bean(Bucket::class)->del($conn);
    }

    /**
     * @param Connection $conn
     */
    public function eventLoop(Connection $conn){
        $reciveListener = new ReceiveListener();
        $fd = $conn->socket->fd;
        while(true) {
            $data = $conn->recv();
            if(empty($data))
                break;
            try {
                Log::info("fd:{$fd} data:{$data}");
                $reciveListener->onReceive($server,$fd,$data);
            } catch (\Throwable $e) {
                $file = $e->getFile();
                $line = $e->getLine();
                $code = $e->getCode();
                $exception = $e->getMessage();
                Log::error("file:" . $file . " line:$line code:$code msg:$exception");
                $server->close($fd);
            }
            //destory context
            Context::compelete();
        }
    }

}