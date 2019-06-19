<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/18
 * Time: 11:04
 */

namespace Core\Http;


use App\Router;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

class HttpRouter
{
    /**
     * @var Dispatcher
     */
    public $dispatcher;
    public static $instance;
    public static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function __construct()
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $route){
            (new Router())->register($route);
        });
        $this->dispatcher = $dispatcher;
    }
}