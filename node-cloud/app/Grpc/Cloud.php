<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/18
 * Time: 11:15
 */

namespace App\GRpc;


use Core\Context\Context;
use Grpc\Parser;
use Im\Cloud\BroadcastReply;

class Cloud
{
    public function broadcast(){
        var_dump("broad");
        //使用 grpc 包根据probuf 格式进行序列化
        $broadcatRp = Parser::serializeMessage(new BroadcastReply());
        //可以根据全局上下文获取reponse
//        return Context::get()
//                       ->getResponse()
//                       ->withContent($broadcatRp);
        //使用助手也可以
        return response()->withContent($broadcatRp);
    }
}