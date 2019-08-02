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
use Co\Client;
use Core\Cloud;
use Core\Context\Context;
use Log\Helper\CLog;
use Swoft\Log\Helper\Log;

class PushMidController extends BaseController
{
    public function mids()
    {
        $post  = Context::get()->getRequest()->input();
        if(empty($post["operation"]) || empty($post["mids"]) ||empty($post["msg"])){
            return $this->error("缺少参数");
        }
        $arg = [
            "op" => $post["operation"],
            "mids" => is_array($post["mids"])?$post["mids"]:[$post["mids"]],
            "msg" => $post["msg"]
        ];
        CLog::info("push mids post data:".json_encode($arg));
        /**
         * @var LogicPush
         */
        container()->get(LogicPush::class)->pushMids($arg["op"],$arg["mids"],$arg["msg"]);
        return $this->success();
    }

}