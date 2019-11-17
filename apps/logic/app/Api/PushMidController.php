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
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use Log\Helper\Log;
use Swoole\Coroutine;
use Task\Task;

/**
 * Class PushMidController
 * @package App\Api
 * @Bean()
 */
class PushMidController extends BaseController
{
    /**
     * @return \Core\Http\Response\Response|static
     * @throws \Exception
     */
    public function mids()
    {
        $post  = Context::get()->getRequest()->input();
        if(empty($post["operation"]) || empty($post["mids"]) ||empty($post["msg"])){
            return $this->error("缺少参数");
        }
        $this->end();
        $arg = [
            "op" => $post["operation"],
            "mids" => is_array($post["mids"])?$post["mids"]:[$post["mids"]],
            "msg" => $post["msg"]
        ];
        Log::debug("push mids post data:".json_encode($arg));
        /**
         * @var LogicPush
         */
        Task::deliver(LogicPush::class,"pushMids",[(int)$arg["op"],$arg["mids"],$arg["msg"]]);
    }

}