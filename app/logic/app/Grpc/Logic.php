<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/18
 * Time: 11:15
 */

namespace App\GRpc;


use App\Lib\LogicConnection;
use Core\Context\Context;
use Grpc\Parser;
use Im\Cloud\BroadcastReply;
use Im\Logic\CloseReply;
use Im\Logic\ConnectReply;
use Im\Logic\ConnectReq;
use Im\Logic\DisconnectReply;
use Im\Logic\DisconnectReq;
use Im\Logic\HeartbeatReply;
use Im\Logic\HeartbeatReq;
use Im\Logic\OnlineReply;
use Im\Logic\OnlineReq;
use Im\Logic\PingReply;

class Logic
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

    /**
     * @return \Core\Http\Response\Response
     * @throws \Exception
     */
    public function connect()
    {
        /**
         * @var ConnectReq
         */
        $req = Parser::deserializeMessage([ConnectReq::class,null],request()->getRawBody());
        $res = container()->get(LogicConnection::class)
                    ->connection(
                        $req->getServer(),
                        $req->getCookie(),
                        $req->getToken()
                    );
        $rpy = new ConnectReply();
        $rpy->setMid($res["mid"]);
        $rpy->setKey($res["key"]);
        $rpy->setRoomID($res["roomID"]);
        $rpy->setAccepts($res["accepts"]);
        return Context::get()
                        ->getResponse()
                        ->withContent(Parser::serializeMessage($rpy));
    }

    /**
     * @return \Core\Http\Response\Response
     */
    public function disConnect()
    {
        /** @var DisconnectReq $req        */
        $req = Parser::deserializeMessage([DisconnectReq::class,null],request()->getRawBody());
        bean(LogicConnection::class)->disConnect($req->getMid(),$req->getKey(),$req->getServer());
        $rpy = Parser::serializeMessage(new DisconnectReply());
        return response()
                    ->withContent($rpy);

    }

    /**
     * @return \Core\Http\Response\Response
     * @throws \Exception
     */
    public function heartBeat()
    {
        /** @var HeartbeatReq $req */
        $req = Parser::deserializeMessage([HeartbeatReq::class,null],request()->getRawBody());
        container()->get(LogicConnection::class)
                        ->heartBeat($req->getMid(),$req->getKey(),$req->getServer());
        $rpy = Parser::serializeMessage(new HeartbeatReply());
        return Context::get()
                        ->getResponse()
                        ->withContent($rpy);
    }

    /**
     * @return \Core\Http\Response\Response
     */
    public function renewOnline()
    {
        /** @var OnlineReq $req */
        $req = Parser::deserializeMessage([OnlineReq::class,null],request()->getRawBody());
        $allRoomCount = bean(LogicConnection::class)
                         ->renewOnline($req->getServer(),$req->getRoomCount());
        $rpy = new OnlineReply();
        $rpy->setAllRoomCount($allRoomCount);
        return response()->withContent(Parser::serializeMessage($rpy));
    }
}