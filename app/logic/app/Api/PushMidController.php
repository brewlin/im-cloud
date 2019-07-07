<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:45
 */

namespace App\logic\app\Api;


class PushMidController
{
    public function mids()
    {
        $post  = Context::get()->getRequest()->input();
        if(empty($post["operation"]) || empty($post["mids"])){
            return $this->error("缺少参数");
        }
        $arg = [
            "op" => $post["operation"],
            "keys" => $post["mids"],
            "msg" => $post[""]
        ];
    }

}