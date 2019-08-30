cloud 主服务节点
==============
概述
=======
+ `grpc-server` 作为grpc服务端，提供grpc访问,并和客户端进行通讯
+ `tcp-server` 作为用户注册的服务中心，可以通过tcp方式连接cloud服务节点
并通过grpc调用locgic节点储存 `用户-节点-token` 等多个映射信息
架构图
+ `websocekt-server` 作为websocket注册服务中心，可以通过websocekt方式连接cloud服务节点， 和tcp-server 通讯主流程相似
df
+ `cloud` 节点注册到consul

## 进度
### done all code 
### test version

## demo
### 1.Grpc-server 路由 和样例demo
```php
//注册grpc-server 接口路由
 $routeCollector->post('/im.cloud.Cloud/Broadcast', '/Grpc/Cloud/broadcast');
//实现该方法
class Cloud
{
    public function broadcast(){
        var_dump("broad");
        //使用 grpc 包根据probuf 格式进行序列化
        $broadcatRp = Parser::serializeMessage(new BroadcastReply());
        //可以根据全局上下文获取reponse
//        return Context::get()
//                       ->getResponse()
//                       ->withContent($broadcatRp);
        //使用助手也可以
        return response()->withContent($broadcatRp);
    }
}
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
