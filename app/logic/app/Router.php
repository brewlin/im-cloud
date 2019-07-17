<?php

namespace App;

use Core\Http\HttpRouter;

//Grpc server router
HttpRouter::post('/im.cloud.Cloud/Broadcast', '/Grpc/Cloud/broadcast');

//consul health check
HttpRouter::get("/health","/Api/HealthController/health");

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
HttpRouter::get("/consumer","/Test/Consumer/con");

