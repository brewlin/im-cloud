<?php

namespace App;


use FastRoute\RouteCollector;

class Router
{
    function register(RouteCollector $routeCollector)
    {
        //broadcast to everyone
        $routeCollector->post('/im.cloud.Cloud/Broadcast', '/Grpc/Cloud/broadcast');

    }
}
