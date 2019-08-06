<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:45
 */

namespace App\Api;


use App\Api\MsgEnum;
use App\Task\LogicPush;
use Co\Client;
use Core\App;
use Core\Cloud;
use Core\Co;
use Core\Context\Context;
use Log\Helper\CLog;
use Log\Helper\Log;
use Task\Task;

class PushMidController extends BaseController
{
    /**
     * @return \Core\Http\Response\Response|static
     */
    public function mids()
    {
        Log::error("sdfs");
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
        Co::create(function ()use($arg){
            Task::deliver(LogicPush::class,"pushMids",[(int)$arg["op"],$arg["mids"],$arg["msg"]]);
//            container()->get(LogicPush::class)->pushMids($arg["op"],$arg["mids"],$arg["msg"]);
        },false);
        return $this->success();
    }

}