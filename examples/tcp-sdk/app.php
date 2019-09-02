<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/2
 * Time: 22:05
 */
require "./client.php";

define("IP","127.0.0.1");
define("PORT","9501");

(new client())->start();