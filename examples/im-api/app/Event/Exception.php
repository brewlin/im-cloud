<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/16 0016
 * Time: 上午 10:17
 */

namespace App\Event;

use Core\Container\Mapping\Bean;
use Core\Contract\AppExceptionInterface;
use Core\Event\EventDispatcherInterface;
use Core\Event\EventEnum;
use Core\Event\Mapping\Event;
use Core\Http\Response\Response;

/**
 * Class Exception
 * @package App\Exception
 * @Event(alias=EventEnum::AppException)
 */
class Exception implements EventDispatcherInterface
{

    /**
     * @param Response $response
     * @param \Throwable $throwable
     * @return Response
     */
    public function dispatch(...$param)
    {
        list($response,$throwable) = $param;
        $err = [
            'code' => 0,
            'msg' => $throwable->getMessage()
        ];
        return $response->withContentType('application/json')
                 ->withStatus(200)
                 ->withContent(json_encode($err))->end();

    }

}