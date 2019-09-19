<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/18
 * Time: 23:06
 */

namespace App;
use App\Packet\Packet;
use App\Service\Service\Dispatcher;
use Core\Contract\ApplicationInterface;
use App\Server\Bucket;
use Core\Context\Context;
use Log\Helper\Log;
use Swoole\Coroutine\Server;
use Swoole\Coroutine\Server\Connection;
use App\Server\Connection as Con;

/**
 * Class TcpServer
 * @package App
 */
class TcpServer implements ApplicationInterface
{
    /**
     * @var Server
     */
    public static $tcpServer;

    /**
     * @return void
     */
    public function handle():void
    {
        //start tcp server
        /** @var Server $server */
        $server = new Server(env("TCP_HOST","0.0.0.0"), env("TCP_PORT",9501), false);
        $server->set([
            'package_max_length' => 1081920,
            'heartbeat_idle_time' => 20,
            'heartbeat_check_interval' => 5,
        ]);
        self::$tcpServer = $server;
        $server->handle([$this,"tcpAccept"]);
        $server->start();
    }

    /**
     * @param Connection $conn
     */
    public function tcpAceept(Connection $conn)
    {
        $conn = new Con($conn,Con::Tcp);
        bean(Bucket::class)->put($conn);
        $this->eventLoop($conn);
        $conn->close();
        bean(Bucket::class)->del($conn);
    }

    /**
     * @param Con $conn
     */
    public function eventLoop(Con $conn){
        //false => disconnect
        while('' !== $data = $conn->recv()) {
            try {
                Log::info("fd:{$conn->getFd()} data:{$data}");
                $this->dispatch($conn->getFd(),$data);
            } catch (\Throwable $e) {
                $file = $e->getFile();
                $line = $e->getLine();
                $code = $e->getCode();
                $exception = $e->getMessage();
                Log::error("file:" . $file . " line:$line code:$code msg:$exception");
                $conn->close();
            }
            //destory context
            Context::compelete();
        }
    }

    /**
     * @param int $fd
     * @param $data
     * @throws \Exception
     */
    public function dispatch(int $fd,$data)
    {
        /** @var Packet $packet */
        $packet = bean(Packet::class)->unpack($data);
        Context::withValue(Packet::class, $packet);
        Context::withValue("fd", $fd);

        //dispatch
        container()->get(Dispatcher::class)
            ->dispatch();
    }

}