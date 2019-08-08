<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:47
 */

namespace App\Api;


use App\Lib\LogicPush;
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use ImQueue\Amqp\Consumer;

/**
 * Class PushAllController
 * @package App\Api
 * @Bean()
 */
class PushAllController extends BaseController
{
    /**
     * @return PushAllController|\Core\Http\Response\Response
     * @throws \Exception
     */
    public function all()
    {
        $post  = Context::get()->getRequest()->input();
        $this->end();
        if(empty($post["operation"]) || empty($post["speed"])){
            return $this->error("缺少参数");
        }
        $arg = [
            "op" => $post["operation"],
            "speed" => $post["speed"],
            "msg" => $post["msg"]
        ];
        container()->get(LogicPush::class)->pushAll($arg["op"],$arg["speed"],$arg["msg"]);
    }

}