<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/18
 * Time: 8:39
 */

namespace Core\Http;


use Core\Container\Container;
use Core\Context\Context;
use Core\Contract\AppExceptionInterface;
use Core\Event\EventEnum;
use Core\Event\EventManager;
use Core\Http\Request\Request;
use Core\Http\Response\ErrMsg;
use Core\Http\Response\Response;
use FastRoute\Dispatcher;
use Log\Helper\CLog;
use Log\Helper\Log;

class HttpDispatcher
{

    public static function dispatch(Request $request,Response $response)
    {
        //manager context
        $context = HttpContext::new($request,$response);
        Context::set($context);
        Log::info("http method:%s ,http url:%s",$request->getMethod(),$request->getUriPath());
        /** @var Dispatcher $dispatcher */
        $dispatcher = Container::getInstance()->get(HttpRouter::class)->dispatcher;
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
                return $response->withStatus(404)->withContent(ErrMsg::err(ErrMsg::NOT_FOUND));
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed  方法不允许
                return $response->withStatus(405)->withContent(ErrMsg::err(ErrMsg::METHOD_NOT_ALLOWED));
                break;
            case Dispatcher::FOUND: // 找到对应的方法
                $rsp = EventManager::trigger(EventEnum::AfterFindRouter,\request(),$response);
                try{
                    $handler = explode("/","App".$routeInfo[1]); // 获得处理函数
                    $action = array_pop($handler);
                    $classname = implode($handler,"\\");
                    $rsp = bean($classname)->$action();
                    if($rsp instanceof Response){
                        return $rsp;
                    }else{
                        return $response->withContent(json_encode($rsp));
                    }
                }catch (\Throwable $e){
                    $rsp = EventManager::trigger(EventEnum::AppException,$response,$e);
                }
                if($rsp instanceof Response)return $rsp;
                break;
        }
        return $response;
    }
}