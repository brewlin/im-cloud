<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/18
 * Time: 11:04
 */

namespace Core\Http;


use Core\Container\Mapping\Bean;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

/**
 * Class HttpRouter
 * @package Core\Http
 * @Bean()
 * @method static post(string $path,string $class):bool
 * @method static get(string $path,string $class):bool
 */
class HttpRouter
{
    /**
     * @var Dispatcher
     */
    public $dispatcher;
    public static $router = [];

    /**
     * @param $name
     * @param $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        self::$router[$name][] = $arguments;
    }

    public function __construct()
    {
//        require_once ROOT . "/app/Router.php";
        $dispatcher = simpleDispatcher(function (RouteCollector $route){
            foreach(self::$router as $method => $r){
                foreach ($r as $r1){
                    $route->{$method}(...$r1);
                }
            }
        });
        $this->dispatcher = $dispatcher;
    }
}