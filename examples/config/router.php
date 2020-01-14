<?php

namespace App;

use Core\Http\HttpRouter;

//Grpc server router
HttpRouter::post('/im.logic.Logic/Ping', '/Grpc/Logic/ping');
HttpRouter::post('/im.logic.Logic/Close', '/Grpc/Logic/close');
HttpRouter::post('/im.logic.Logic/Connect', '/Grpc/Logic/connect');
HttpRouter::post('/im.logic.Logic/Disconnect', '/Grpc/Logic/disConnect');
HttpRouter::post('/im.logic.Logic/Heartbeat', '/Grpc/Logic/heartBeat');
HttpRouter::post('/im.logic.Logic/RenewOnline', '/Grpc/Logic/renewOnline');

//---- push
HttpRouter::post('/im.logic.Logic/PushKeys', '/Grpc/LogicPush/pushKeys');
HttpRouter::post('/im.logic.Logic/PushMids', '/Grpc/LogicPush/pushMids');
HttpRouter::post('/im.logic.Logic/PushAll', '/Grpc/LogicPush/pushAll');
HttpRouter::post('/im.logic.Logic/PushRoom', '/Grpc/LogicPush/pushRoom');

//consul health check
HttpRouter::get("/health","/Api/HealthController/health");

//api router
HttpRouter::post("/im/push/keys","/Api/PushKeyController/keys");
HttpRouter::post("/im/push/mids","/Api/PushMidController/mids");
HttpRouter::post("/im/push/room","/Api/PushRoomController/room");
HttpRouter::post("/im/push/all","/Api/PushAllController/all");

//query online status
HttpRouter::get("/im/online/top","/Api/OnlineController/top");
HttpRouter::get("/im/online/room","/Api/OnlineController/room");
HttpRouter::get("/im/online/total","/Api/OnlineController/total");

//query nodes
HttpRouter::get("/im/nodes/instances","/Api/NodeController/instances");
//consumer producer test
HttpRouter::get("/consumer","/Test/Consumer/con");
HttpRouter::get("/producer","/Test/Consumer/pro");

