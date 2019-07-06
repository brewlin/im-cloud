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
require_once ROOT."/app/Router.php";

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
    public static $router;

    /**
     * @param $name
     * @param $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        self::$router[$name] = $arguments;
    }

    public function __construct()
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $route){
            foreach(self::$router as $method => $r){
                $route->{$method}(...$r);
            }
        });
        $this->dispatcher = $dispatcher;
    }
}