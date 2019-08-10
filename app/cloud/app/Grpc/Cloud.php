<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/18
 * Time: 11:15
 */

namespace App\Grpc;


use App\Lib\Broadcast;
use App\Packet\Task;
use App\Service\Dao\Bucket;
use App\Service\Dao\Push;
use App\Service\Dao\Room;
use Core\Co;
use Core\Container\Mapping\Bean;
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
use Log\Helper\Log;
use Swoole\Coroutine;

/**
 * Class Cloud
 * @package App\Grpc
 * @Bean()
 */
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

    /**
     *
     */
    public function pushMsg()
    {
        $pushMsgRpy = Parser::serializeMessage(new PushMsgReply());
        /** @var PushMsgReq $pushMsgReq */
        $pushMsgReq = Parser::deserializeMessage([PushMsgReq::class,null],request()->getRawBody());
        response()->withContent($pushMsgRpy)->end();

        if(empty($pushMsgReq->getKeys()) || empty($pushMsgReq->getProto())){
            Log::error("cloud grpc pushmsg keys proto is empty raw data:".json_encode($pushMsgReq));
            return;
        }
        /** @var array $keys */
        $keys = $pushMsgReq->getKeys();
        $op = $pushMsgReq->getProtoOp();
        $body = $pushMsgReq->getProto()->getBody();
        //coroutine do
        foreach ($keys as $key){
            Coroutine::create(function ()use($key,$body,$op){
                    /** @var Task $task */
                    \bean(Task::class)->deliver(Push::class,"push",[$key,$op,$body]);
            });
        }

    }

    /**
     * @return \Core\Http\Response\Response|static|void
     */
    public function broadcast(){
        $broadcastRpy = Parser::serializeMessage(new BroadcastReply());
        /** @var BroadcastReq $broadcastReq */
        $broadcastReq = Parser::deserializeMessage(
                                                    [BroadcastReq::class,null],
                                                    Context::get()->getRequest()->getRawBody()
        );
        response()->withContent($broadcastRpy)->end();
        if(empty($broadcastReq->getProto())){
            return;
        }
        Co::create(function ()use($broadcastReq){
            Broadcast::push($broadcastReq->getProto(),$broadcastReq->getProtoOp());
        });
    }

    /**
     * @return \Core\Http\Response\Response|static|void
     */
    public function broadcastRoom()
    {
        $broadroomRpy = Parser::serializeMessage(new BroadcastRoomReply());
        /** @var BroadcastRoomReq $broadroomReq */
        $broadroomReq = Parser::deserializeMessage(
            [BroadcastRoomReq::class,null],
            request()->getRawBody()
        );
        response()->withContent($broadroomRpy)->end();
        Log::info("broadcastRoom req:".json_encode($broadroomReq));
        if(empty($broadroomReq->getProto()) || empty($broadroomReq->getRoomID())){
            return;
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
                                 Parser::serializeMessage($roomRpy)
                             );
    }
}