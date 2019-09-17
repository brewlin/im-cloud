<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/12 0012
 * Time: 下午 2:56
 */

namespace App;

use Core\App;
use App\Consumer\Consumer;
use Core\Co;
use Core\Console\Console;
use Core\Contract\ApplicationInterface;
use Core\Http\HttpDispatcher;
use Log\Helper\CLog;
use Swoole\Coroutine\Http\Server;
use Core\Http\Request\Request as HttpRequest;
use Core\Http\Response\Response as HttpResponse;

/**
 * Class Application
 * @package App
 */
class Application extends App implements ApplicationInterface
{
    public function handle():void
    {
        Co::create(function (){
            try{
                consumer()->consume(new Consumer());
            }catch (\Throwable $e){
                Console::write("<error>{$e->getMessage()}</error>");
            }
        });
    }
}