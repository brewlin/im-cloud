<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:47
 */

namespace App\Api;
use App\Lib\Logic;
use App\Lib\LogicOnline;
use Core\Context\Context;

/**
 * @package Online
 */
class OnlineController extends BaseController
{
    /**
     * @return OnlineController|\Core\Http\Response\Response
     * @throws \Exception
     */
    public function top()
    {
        $post  = Context::get()->getRequest()->input();
        if(empty($post["type"]) || empty($post["limit"])){
            return $this->error("缺少参数");
        }
        $res  = \container()
            ->get(LogicOnline::class)
            ->onlineTop($post["type"],$post["limit"]);
        return $this->success($res);

    }

    /**
     * @return OnlineController|\Core\Http\Response\Response
     * @throws \Exception
     */
    public function room()
    {
        $post  = Context::get()->getRequest()->input();
        if(empty($post["type"]) || empty($post["rooms"])){
            return $this->error("缺少参数");
        }
        $res = \container()
            ->get(LogicOnline::class)
            ->onlineRoom($post["type"],$post["rooms"]);
        return $this->success($res);
    }

}