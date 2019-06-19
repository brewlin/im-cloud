<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/18
 * Time: 8:39
 */

namespace Core\Http;


use Core\Context\Context;
use Core\Http\Request\Request;
use Core\Http\Response\Response;
use FastRoute\Dispatcher;
use Swoft\Log\Helper\CLog;

class HttpDispatcher
{
    const METHOD_NOT_ALLOWED = "not support this method";
    const NOT_FOUND = "not found that action";
    const SERVER_ERROR =" internel error";
    public static function dispatch(Request $request,Response $response)
    {
        //manager context
        $context = HttpContext::new($request,$response);
        Context::set($context);
        CLog::info("http method:%s ,http url:%s",$request->getMethod(),$request->getUriPath());
        //dispatche route
        $dispatcher = HttpRouter::getInstance()->dispatcher;
        $routeInfo = $dispatcher->dispatch($request->getMethod(),$request->getUriPath());
        $response = self::process($response,$routeInfo);
        //destory context
        $response->send();
        Context::compelete();

    }

    /**
     * dispatcher process
     * @param Response $response
     * @param array $routeInfo
     * @return Response
     */
    public static function process(Response $response,array $routeInfo):Response
    {
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found 没找到对应的方法
                $response->withStatus(404)->withContent(self::NOT_FOUND);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed  方法不允许
                $response->withStatus(405)->withContent(self::METHOD_NOT_ALLOWED);
                break;
            case Dispatcher::FOUND: // 找到对应的方法
                try{
                    $handler = explode("/","/App".$routeInfo[1]); // 获得处理函数
                    $action = array_pop($handler);
                    $classname = implode($handler,"\\");
                    $rsp = (new $classname())->$action();
                    if($rsp instanceof Response){
                        $response = $rsp;
                    }else{
                        $response->withContent(json_encode($rsp));
                    }
                }catch (\Throwable $e){
                    CLog::info("router dispatcher is error:".$e->getMessage());
                    $response->withStatus(500)
                        ->withContent($e->getMessage());
                }
                break;
        }
        return $response;
    }
}