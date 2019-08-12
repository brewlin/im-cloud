<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:46
 */

namespace App\Api;


use App\Task\LogicPush;
use Core\Container\Mapping\Bean;
use Core\Context\Context;

/**
 * Class PushRoomController
 * @package App\Api
 * @Bean()
 */
class PushRoomController extends BaseController
{
    /**
     * @return PushRoomController|\Core\Http\Response\Response
     * @throws \Exception
     */
    public function room(){
        $post  = Context::get()->getRequest()->input();
        $this->end();
        if(empty($post["operation"]) || empty($post["room"]) || empty($post["type"]) | empty($post['msg'])){
            return $this->error("缺少参数");
        }
        $arg = [
            "op" => $post["operation"],
            "type" => $post["type"],
            "room" => $post["room"],
            'msg' => $post['msg']
        ];
        container()->get(LogicPush::class)->pushRoom($arg["op"],$arg["type"],$arg["room"],$arg["msg"]);
    }

}