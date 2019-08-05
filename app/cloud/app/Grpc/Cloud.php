<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/18
 * Time: 11:15
 */

namespace App\GRpc;


use App\Lib\Broadcast;
use App\Packet\Task;
use App\Service\Dao\Bucket;
use App\Service\Dao\Push;
use App\Service\Dao\Room;
use Core\Co;
use Core\Context\Context;
use Grpc\Parser;
use Im\Cloud\BroadcastReply;
use Im\Cloud\BroadcastReq;
use Im\Cloud\BroadcastRoomReply;
use Im\Cloud\BroadcastRoomReq;
use Im\Cloud\PushMsgReply;
use Im\Cloud\PushMsgReq;
use Im\Cloud\RoomsReply;
use Im\Logic\CloseReply;
use Im\Logic\PingReply;
use Log\Helper\CLog;

class Cloud
{
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
        /** @var array $keys */
        $keys = $pushMsgReq->getKeys();
        $data = $pushMsgReq->getProtoOp();
        //coroutine do
        foreach ($keys as $key){
            Co::create(function ()use($key,$data){
                    /** @var Task $task */
                    $task = \bean(Task::class);
                    $task->setClass(Push::class);
                    $task->setMethod("push");
                    $task->setArg([$key,$data]);
                    $task->exec();
    //                bean(Push::class)->push($key,$data);
            },false);
        }
        return response()->withContent($pushMsgRpy);

    }

    /**
     * @return \Core\Http\Response\Response|static
     */
    public function broadcast(){
        $broadcastRpy = Parser::serializeMessage(new BroadcastReply());
        /** @var BroadcastReq $broadcastReq */
        $broadcastReq = Parser::deserializeMessage(
                                                    [BroadcastReq::class,null],
                                                    Context::get()->getRequest()->getRawBody()
        );
        if(empty($broadcastReq->getProto())){
            return Context::get()->getResponse()->withContent($broadcastRpy);
        }
        Co::create(function ()use($broadcastReq){
            Broadcast::push($broadcastReq->getProto(),$broadcastReq->getProtoOp());
        });
        //使用 grpc 包根据probuf 格式进行序列化
        return response()->withContent($broadcastRpy);
    }

    /**
     * @return \Core\Http\Response\Response|static
     */
    public function broadcastRoom()
    {
        $broadroomRpy = Parser::serializeMessage(new BroadcastRoomReply());
        /** @var BroadcastRoomReq $broadroomReq */
        $broadroomReq = Parser::deserializeMessage(
            [BroadcastRoomReq::class,null],
            request()->getRawBody()
        );
        CLog::info("broadcastRoom req:".json_encode($broadroomReq));
        if(empty($broadroomReq->getProto()) || empty($broadroomReq->getRoomID())){
            return response()->withContent($broadroomRpy);
        }
        //go coroutine
        Co::create(function()use($broadroomReq){
            foreach(Bucket::buckets() as $roomId){
                Room::push($roomId,$broadroomReq);
            }
        },false);


    }
    /**
     * @return \Core\Http\Response\Response|static
     */
    public function rooms()
    {
        $roomRpy = new RoomsReply();
        $roomids = Bucket::buckets();
        $roomRpy->setRooms($roomids);
        return Context::get()->getResponse()
                             ->withContent(
                                 $roomRpy->serializeToJsonString()
                             );
    }
}