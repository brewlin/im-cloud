<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/22
 * Time: 15:59
 */

namespace Core;


use Core\Container\Container;
use Core\Http\HttpRouter;
use Core\Http\Request\Request;
use Core\Http\Response\Response;

class Autoloader implements \Core\Contract\Autoloader
{
    public function handler()
    {
        Container::getInstance()->create(HttpRouter::class);
        Container::getInstance()->create(Request::class);
        Container::getInstance()->create(Response::class);
    }

}