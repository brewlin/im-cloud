<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/16 0016
 * Time: 上午 10:17
 */

namespace App\Exception;

use Core\Container\Mapping\Bean;
use Core\Contract\AppExceptionInterface;
use Core\Http\Response\Response;

/**
 * Class Exception
 * @package App\Exception
 * @Bean()
 */
class Exception implements AppExceptionInterface
{
    public function handlerException(Response $response, \Throwable $throwable):Response
    {
        $err = [
            'code' => 0,
            'msg' => $throwable->getMessage()
        ];
        return $response->withHeader('content-type','application/json')
                 ->withStatus(200)
                 ->withContent(json_encode($err));

    }

}