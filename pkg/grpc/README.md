im-cloud grpc composer包 
==============
概述
=======
+ 使用 `protobuf` 构建grpc 接口
+ /example 下有client 请求例子
+ /bin 存放 cloud.proto logic.proto
+ /使用protobuf 生成gprc 接口
    + `./bin/gen.sh`
## 生成工具脚本
```proto
protoc.exe --php_out=${cloud} --grpc_out=${cloud} --plugin=protoc-gen-grpc=./grpc_php_plugin.exe  cloud.proto
protoc.exe --php_out=${logic} --grpc_out=${logic} --plugin=protoc-gen-grpc=./grpc_php_plugin.exe  logic.proto

```
## 不要随意改变grpc 依赖
> [example demo](example) 客户端调用demo
```php
// 客户端调用
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
```

