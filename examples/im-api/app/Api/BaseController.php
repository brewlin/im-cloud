<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/15 0000 0028
 * Time: 下午 5:43
 */

namespace App\Api;
use App\Lib\MsgEnum;
use App\Services\UserCacheService;
use Core\Context\Context;

/**
 * Class PushKeyController
 * @package App\logic\app\Api
 */
class BaseController
{
    /**
     * @var array
     */
    protected $user = [];
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

    /**
     * init user cache
     * @return void
     */
    public function getCurrentUser()
    {

    }

}