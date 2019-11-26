<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:43
 */

namespace App\Api;
use App\Task\LogicPush;
use Core\Co;
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use Im\Cloud\Operation;
use Task\Task;

/**
 * Class PushKeyController
 * @package App\logic\app\Api
 * @Bean()
 */
class PushKeyController extends BaseController
{
    /**
     * @param [
     *      'operation' => 9
     *      'keys' => ['key1',key2',key3']
     *      'msg' =>  body
     * ]
     * @return \Core\Http\Response\Response|static
     */
    public function keys()
    {
        $post  = Context::get()->getRequest()->input();
        if(empty($post["msg"]) || empty($post["keys"])){
            return $this->error("缺少参数");
        }
        $this->end();
        $arg = [
            "keys" => is_array($post["keys"])?$post["keys"]:[$post["keys"]],
            "msg" => $post["msg"]
        ];
        /**
         * @var LogicPush
         */
        Co::create(function()use($arg){
            LogicPush::pushKeys(Operation::OpRaw,$arg["keys"],$arg["msg"]);
        });
    }

}