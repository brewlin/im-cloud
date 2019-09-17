<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:43
 */

namespace App\Api;
use App\Api\MsgEnum;
use Core\Context\Context;

/**
 * Class PushKeyController
 * @package App\logic\app\Api
 */
class BaseController
{
    public function error($msg = "",$data = [],$code = MsgEnum::Error)
    {
        $data = [
            "msg" => $msg,
            "data" => $data,
            "code" => $code
        ];
        return Context::get()->getResponse()
                              ->withStatus($code)
                              ->withContentType("application/json")
                              ->withContent($data);
    }
    public function success($data = [],$msg = "",$code = MsgEnum::Ok)
    {
         $data = [
            "msg" => $msg,
            "data" => $data,
            "code" => $code
        ];
        return Context::get()->getResponse()
                              ->withStatus($code)
                              ->withContentType("application/json")
                              ->withContent($data);
    }
    public function end($data = ["ok"],$msg = "",$code = MsgEnum::Ok)
    {
        $data = [
            "msg" => $msg,
            "data" => $data,
            "code" => $code
        ];
        Context::get()->getResponse()
            ->withStatus($code)
            ->withContentType("application/json")
            ->withContent($data)
            ->end();
    }

}