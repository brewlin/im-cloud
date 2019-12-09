<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/11/25
 * Time: 11:15
 */

namespace App\Grpc;


use App\Connection\Bucket;
use App\Service\Dao\Broadcast;
use App\Packet\Task;
use App\Service\Dao\BroadcastRoom;
use App\Service\Dao\Push;
use Core\Co;
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use Grpc\Parser;
use Im\Cloud\Operation;
use Im\Logic\PushAllReply;
use Im\Logic\PushAllReq;
use Im\Logic\PushKeysReply;
use Im\Logic\PushKeysReq;
use Im\Logic\PushMidsReply;
use Im\Logic\PushMidsReq;
use Im\Logic\PushRoomReply;
use Im\Logic\PushRoomReq;
use Log\Helper\Log;

/**
 * Class Cloud
 * @package App\Grpc
 * @Bean()
 */
class LogicPush
{
    /**
     * 接受 cloud节点 grpc请求，单点推送消息
     * @return void
     */
    public function pushKeys()
    {
        Log::debug("logic node: pushkey");
        $pushKeysRpy = Parser::serializeMessage(new PushKeysReply());
        /** @var PushKeysReq $pushKeysReq */
        $pushKeysReq = Parser::deserializeMessage([PushKeysReq::class,null],request()->getRawBody());
        response()->withContent($pushKeysRpy)->end();

        /** @var array $keys */
        $keys = [];
        foreach($pushKeysReq->getKeys() as $key){
            $keys[] = $key;
        }
        $msg = $pushKeysReq->getMsg();
        if(empty($keys) || empty($msg))
        {
            Log::error("logic grpc push keys proto is empty raw data:");
            return;
        }
        Co::create(function ()use($keys,$msg){
            \App\Task\LogicPush::pushKeys(Operation::OpRaw,$keys,$msg);
        });

    }

    /**
     * @return \Core\Http\Response\Response|static|void
     */
    public function pushMids(){
        $pushMidsReply = Parser::serializeMessage(new PushMidsReply());

        /** @var PushMidsReq $pushMidsReq */
        $pushMidsReq = Parser::deserializeMessage([
            PushMidsReq::class,null],
            Context::get()->getRequest()->getRawBody()
        );

        response()->withContent($pushMidsReply)->end();
        /** @var array $mids */
        $mids = [] ;
        foreach($pushMidsReq->getMids() as $mid){
            $mids[] = $mid;
        }
        $msg = $pushMidsReq->getMsg();
        if(empty($msg) || empty($msg)){
            Log::error("require msg and mids");
            return;
        }
        Log::debug("push mids post data");
        Co::create(function ()use($msg,$mids){
            \App\Task\LogicPush::pushMids(Operation::OpRaw,$mids,$msg);
        });
    }

    /**
     * 接受cloud节点  grpc请求，并进行房间广播
     * @return \Core\Http\Response\Response|static|void
     */
    public function pushAll()
    {
        $pushAllReply = Parser::serializeMessage(new PushAllReply());
        /** @var PushAllReq $pushAllReq */
        $pushAllReq = Parser::deserializeMessage(
            [
                PushAllReq::class,null
            ],
            request()->getRawBody()
        );
        response()->withContent($pushAllReply)->end();
        Log::info("broadcast req");
        $msg = $pushAllReq->getMsg();
        if(empty($msg)){
            Log::error("logic pushall require msg");
            return;
        }
        \App\Task\LogicPush::pushAll(Operation::OpRaw,$msg);

    }
    /**
     * @return \Core\Http\Response\Response|static
     */
    public function pushRoom()
    {
        $roomRpy = new PushRoomReply();

        /** @var PushRoomReq $roomReq */
        $roomReq = Parser::deserializeMessage(
            [PushRoomReq::class,null],
            request()->getRawBody()
        );

        $type = $roomReq->getType();
        $room = $roomReq->getRoom();
        $msg = $roomReq->getMsg();
        if(empty($type) || empty($room) || empty($msg)){

            Log::error("logic pushroom require data");
            return;
        }
        \App\Task\LogicPush::pushRoom(Operation::OpRaw,$type,$room,$msg);
    }
}