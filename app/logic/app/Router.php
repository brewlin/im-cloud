<?php

namespace App;

use Core\Http\HttpRouter;

//Grpc server router
HttpRouter::post('/im.cloud.Cloud/Broadcast', '/Grpc/Cloud/broadcast');

//consul health check
HttpRouter::get("/health","/Api/HealthController/health");
