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
use Im\Cloud\PushMsgReply;
use Im\Cloud\PushMsgReq;
use Im\Logic\CloseReply;
use Im\Logic\PingReply;
use Log\Helper\CLog;

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
    /**
     * @return \Core\Http\Response\Response
     */
    public function ping()
    {
        $pingRp = Parser::serializeMessage(new PingReply());
        return Context::get()
            ->getResponse()
            ->withContent($pingRp);
    }

    /**
     * @return \Core\Http\Response\Response
     */
    public function close()
    {
        $closeRp = Parser::serializeMessage(new CloseReply());
        return Context::get()
            ->getResponse()
            ->withContent($closeRp);
    }
    public function pushMsg()
    {
        $pushMsgRpy = Parser::serializeMessage(new PushMsgReply());
        /** @var PushMsgReq $pushMsgReq */
        $pushMsgReq = Parser::deserializeMessage([PushMsgReq::class,null],request()->getRawBody());

        if(empty($pushMsgReq->getKeys()) || empty($pushMsgReq->getProto())){
            CLog::error("cloud grpc pushmsg keys proto is empty raw data:".json_encode($pushMsgReq));
            return response()->withContent($pushMsgRpy);
        }
//        foreach ($pushMsgReq->getKeys())


    }
}