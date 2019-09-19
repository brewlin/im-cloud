<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/18
 * Time: 23:06
 */

namespace App;
use App\Event\Close;
use App\Packet\Packet;
use App\Service\Service\Dispatcher;
use Core\Contract\ApplicationInterface;
use App\Connection\Bucket;
use Core\Context\Context;
use Log\Helper\Log;
use Swoole\Coroutine\Server;
use Swoole\Coroutine\Server\Connection;
use App\Connection\Connection as Con;

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
        bean(Bucket::class)->addConn($conn);
        $this->eventLoop($conn);
        $conn->close();
        bean(Bucket::class)->delConn($conn);
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
                Log::error(
                    sprintf("file:%s line:%s code:%s msg:%s",
                        $e->getFile(),$e->getLine(),$e->getCode(),$e->getMessage())
                );
                $conn->close();
                bean(Close::class)->close($conn);
                /** @var Bucket $bucket */
                $bucket = bean(Bucket::class);
                $bucket->pop($conn->getKey(),$conn->getFd());
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