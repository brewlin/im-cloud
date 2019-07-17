<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:45
 */

namespace App\Api;


use App\Api\MsgEnum;
use App\Lib\LogicPush;
use Core\Context\Context;
use Swoft\Log\Helper\Log;

class PushMidController extends BaseController
{
    public function mids()
    {
        $post  = Context::get()->getRequest()->input();
        if(empty($post["operation"]) || empty($post["mids"])){
            return $this->error("缺少参数");
        }
        $arg = [
            "op" => $post["operation"],
            "keys" => $post["mids"],
            "msg" => $post[""]
        ];
        /**
         * @var LogicPush
         */
        container()->get(LogicPush::class)->pushMids($arg["op"],$arg["keys"],$arg["msg"]);
        return $this->success();
    }

}