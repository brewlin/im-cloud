<?php

namespace App;

use Core\Http\HttpRouter;

//login & register
HttpRouter::post("/login","/Api/LoginController/login");
HttpRouter::post("/register","/Api/LoginController/register");

//init data
HttpRouter::post("/init","/Api/InitController/init");
//group
HttpRouter::get("/api/im/members","/Api/GroupController/getMembers");

