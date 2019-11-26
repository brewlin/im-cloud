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
use Im\Cloud\Operation;
use Task\Task;

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
        if(empty($post["room"]) || empty($post["type"]) | empty($post['msg'])){
            return $this->error("缺少参数");
        }
        $this->end();
        $arg = [
            "type" => $post["type"],
            "room" => $post["room"],
            'msg' => $post['msg']
        ];
        /** Task::deliver */
        Task::deliver(LogicPush::class,"pushRoom",[Operation::OpRaw,$arg['type'],$arg['room'],$arg['msg']]);
    }

}