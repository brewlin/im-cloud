<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:47
 */

namespace App\Api;


use App\Task\LogicPush;
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use Im\Cloud\Operation;
use ImQueue\Amqp\Consumer;
use Task\Task;

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
        if(empty($post["msg"])){
            return $this->error("缺少参数");
        }
        $this->end();
        $arg = [
            "msg" => $post["msg"]
        ];
        Task::deliver(LogicPush::class,"pushAll",[Operation::OpRaw,$arg['msg']]);
    }

}