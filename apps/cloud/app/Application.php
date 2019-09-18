<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/12 0012
 * Time: 下午 2:56
 */

namespace App;

use App\Tcp\Bucket;
use App\Tcp\ReceiveListener;
use Core\App;
use Core\Co;
use Core\Context\Context;
use Core\Contract\ApplicationInterface;
use Swoole\Coroutine\Http\Server as HttpServer;
use Swoole\Coroutine\Server as TcpServer;
use Swoole\Coroutine\Server\Connection;
use Core\Http\Request\Request as HttpRequest;
use Core\Http\Response\Response as HttpResponse;
use Core\Http\HttpDispatcher;
use Swoole\Server;

/**
 * Class Application
 * @package App
 */
class Application extends App implements ApplicationInterface
{
    /**
     * coroutine server processor
     * @var array
     */
    protected $processor = [
        TcpProcessor::class,
        WebsocketProcessor::class,
    ];

    public function handle(): void
    {
        foreach ($this->processor as $processor){
            (new $processor())->handle();
        }
    }
}