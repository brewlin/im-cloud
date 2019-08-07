<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/7
 * Time: 21:11
 */

namespace Im\Cloud;


class Operation
{
	// OpHandshake handshake
	const OpHandshake = 0;
	// OpHandshakeReply handshake reply
	const OpHandshakeReply = 1;

	// OpHeartbeat heartbeat
	const OpHeartbeat = 2;
	// OpHeartbeatReply heartbeat reply
	const OpHeartbeatReply = 3;

	// OpSendMsg send message.
	const OpSendMsg = 4;
	// OpSendMsgReply  send message reply
	const OpSendMsgReply = 5;

	// OpDisconnectReply disconnect reply
	const OpDisconnectReply = 6;

	// OpAuth auth connnect
	const OpAuth = 7;
	// OpAuthReply auth connect reply
	const OpAuthReply = 8;

	// OpRaw raw message
	const OpRaw = 9;

	// OpProtoReady proto ready
	const OpProtoReady = 10;
	// OpProtoFinish proto finish
	const OpProtoFinish = 1;

	// OpChangeRoom change room
	const OpChangeRoom = 12;
	// OpChangeRoomReply change room reply
	const OpChangeRoomReply = 13;

	// OpSub subscribe operation
	const OpSub = 14;
	// OpSubReply subscribe operation
	const OpSubReply = 15;

	// OpUnsub unsubscribe operation
	const OpUnsub = 16;
	// OpUnsubReply unsubscribe operation reply
	const OpUnsubReply = 17;

}