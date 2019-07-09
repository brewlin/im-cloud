<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:47
 */

namespace App\logic\app\Api;


use App\Lib\LogicPush;
use Core\Context\Context;

class PushAllController extends BaseController
{
    public function all()
    {
        $post  = Context::get()->getRequest()->input();
        if(empty($post["operation"]) || empty($post["speed"])){
            return $this->error("缺少参数");
        }
        $arg = [
            "op" => $post["operation"],
            "speed" => $post["speed"],
            "msg" => $post["msg"]
        ];
        container()->get(LogicPush::class)->pushAll($arg["op"],$arg["speed"],$arg["msg"]);
        return $this->success();
    }

}