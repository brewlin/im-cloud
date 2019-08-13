<?php

namespace App;

use Core\Http\HttpRouter;

//Grpc server router
HttpRouter::post('/im.cloud.Cloud/Ping', '/Grpc/Cloud/ping');
HttpRouter::post('/im.cloud.Cloud/Close', '/Grpc/Cloud/close');

HttpRouter::post('/im.cloud.Cloud/PushMsg', '/Grpc/Cloud/pushMsg');
HttpRouter::post('/im.cloud.Cloud/Broadcast', '/Grpc/Cloud/broadcast');
HttpRouter::post('/im.cloud.Cloud/BroadcastRoom', '/Grpc/Cloud/broadcastRoom');
HttpRouter::post('/im.cloud.Cloud/Rooms', '/Grpc/Cloud/rooms');



//consul health check
HttpRouter::get("/health","/Api/HealthController/health");
