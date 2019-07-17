<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:47
 */

namespace App\Api;
use App\Lib\LogicOnline;
use Core\Context\Context;

/**
 * @package Online
 */
class OnlineController extends BaseController
{
    public function top()
    {
        $post  = Context::get()->getRequest()->input();
        if(empty($post["type"]) || empty($post["limit"])){
            return $this->error("缺少参数");
        }
        \container()
            ->get(LogicOnline::class)
            ->onlineTop($post["type"],$post["limit"]);
        return $this->success();

    }
    public function room()
    {

    }
    public function total()
    {

    }

}