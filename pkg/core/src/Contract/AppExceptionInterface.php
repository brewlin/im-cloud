<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/16 0016
 * Time: 上午 10:18
 */

namespace Core\Contract;


use Core\Http\Response\Response;

/**
 * Interface AppExceptionInterface
 * @package Core\Contract
 */
interface AppExceptionInterface
{
    public function handlerException(Response $response, \Throwable $throwable):Response;
}