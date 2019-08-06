<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:43
 */

namespace App\Api;
use App\Task\LogicPush;
use Core\Context\Context;

/**
 * Class PushKeyController
 * @package App\logic\app\Api
 */
class PushKeyController extends BaseController
{
    public function keys()
    {
        $post  = Context::get()->getRequest()->input();
        if(empty($post["operation"]) || empty($post["keys"])){
            return $this->error("缺少参数");
        }
        $arg = [
            "op" => $post["operation"],
            "keys" => $post["keys"],
            "msg" => $post["msg"]
        ];
        container()->get(LogicPush::class)->pushKeys($arg["op"],$arg["keys"],$arg["msg"]);
        return $this->success();
    }

}