<?php

namespace App;

use Core\Http\HttpRouter;

//consul health check
HttpRouter::get("/index","/Api/IndexController/index");
