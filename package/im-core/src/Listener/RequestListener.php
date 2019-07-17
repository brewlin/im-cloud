<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/15
 * Time: 15:34
 */

namespace Core\Listener;


use Core\Co;
use Core\Http\HttpDispatcher;
use Core\Http\Request\Request as HttpRequest;
use Core\Http\Response\Response as HttpResponse;
use Core\Swoole\RequestInterface;
use Log\Helper\CLog;
use Swoole\Http\Request;
use Swoole\Http\Response;

class RequestListener implements RequestInterface
{
    public function onRequest(Request $request, Response $response): void
    {
        $request = HttpRequest::new($request);
        $response = HttpResponse::new($response);
        HttpDispatcher::dispatch($request,$response);
        // TODO: Implement onRequest() method.
    }

}