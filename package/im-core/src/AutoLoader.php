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

class AutoLoader implements \Core\Contract\LoaderInterface
{
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
        // TODO: Implement getPrefixDirs() method.
    }

}