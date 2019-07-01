<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:43
 */

namespace App\logic\app\Api;
use Core\Context\Context;

/**
 * Class PushKeyController
 * @package App\logic\app\Api
 */
class BaseController
{
    public function error($msg = "",$data = [],$code = 404)
    {
        $data = [
            "msg" => $msg,
            "data" => $data,
            "code" => $code
        ];
        return Context::get()->getResponse()
                              ->withStatus($code)
                              ->withContent($data);
    }
    public function success($data = [],$msg = "",$code = 200)
    {
         $data = [
            "msg" => $msg,
            "data" => $data,
            "code" => $code
        ];
        return Context::get()->getResponse()
                              ->withStatus($code)
                              ->withContent($data);
    }

}