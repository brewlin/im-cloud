<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/13 0013
 * Time: 上午 9:36
 */
require_once dirname(__DIR__)."/vendor/autoload.php";
go(function() {
    $client = new \Im\Cloud\CloudClient("127.0.0.1:9500", []);
    $client->start();
    $v = [
        "ver" => 1,
        "op" => 1000,
        "body" => "this is php client"
    ];
    $p = new Im\Cloud\Proto($v);
    $pushMsg = [
        "protoOp" => 1000,
        "proto" => $p,
    ];
    $req = new Im\Cloud\BroadcastReq($pushMsg);
    $res = $client->Broadcast($req);
    var_dump($res);
});

