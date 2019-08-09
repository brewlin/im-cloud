# im-cloud 基于swoole 原生协程构建分布式推送中间件

## 一、概述
> 基于`swoole`原生协程构建商业化即时推送im服务中间件,不进行业务处理，单独作为中间件使用，可弹性扩充节点增加性能处理.不依赖外部框架，核心代码为原生swoole构建的组件

借鉴goim(`bilibili出品,生产级百万消息秒级推送`)，使用swoole实现基于php的高性能分布式im中间件，提升高并发性能的推送
## 二、服务业务节点
> `cloud,job,logic` 等节点都可以水平扩容
- 例如在消费能力不足时可以启动n个`job`节点提高并消费能力
- 启动多个cloud节点作为client客户端负载均衡，将多个`websocket`，`tcp` client分布到多个cloud节点中，提高cloud节点中心处理能力
- `logic` 提供对外restapi 作为主要业务节点
- `高性能` 协程化、水平扩容、分布式服务架构、接入服务治理
### @cloud
[`cloud`](./app/cloud) 作为中心服务节点 `grpc-server` 节点，对外接收TCP、Websocket客户端进行`长连接`,可以水平扩容至多个节点 并注册到服务中心，例如`consul`。每个cloud节点维护自己的客户端
### @job
[`job`](./app/-job) 节点作为消费节点 消费队列数据 然后进行`grpc` 和cloud服务进行通讯 进行 `push` `push room` `broadcast`,作为节点中间件，消费`kafaka`，`rockermq。。。`之类，可以扩展多个节点提高并发消费能力

### @logic 
[`logic`](./app/logic) 节点 提供rest api接口，作为生产节点 和  grpc客户端,可写入队列作为生产者，也可以扩展自己的业务进行grpc直接调用cloud节点中心进行推送

## 三、组件依赖包 `package`
> 服务间配置独立，使用composer进行依赖管理，进行composer组件化开发
+ [`im-core`](./package/im-core) 为核心基础组件，底层设计借鉴 `swoft`源码设计
+ [`im-grpc`](./package/im-grpc) 定义grpc接口规范composer包,使用`protobuf`构建,封装有连接池
+ [`im-discovery`](./package/im-discovery) 服务发现注册组件，注册`grpc-server`，发现服务等封装
+ [`im-process`](./package/im-process) 进程管理模块，可以注册启动自定义进程，并交由swoole master进程管理声明周期
+ [`im-queue`](./package/im-queue) 消息队列管理模块，提供消息队列接口，底层实现了`连接池`接口，无需管理连接，根据类型可以切换不同的消息队列(`done rabbitmq`,soon kafak)
+ [`im-redis`](./package/im-redis) 封装了连接池版本的redis client
+ [`im-task`](./package/im-task) 异步任务组件，封装投递task进程任务的接口，目前仅支持投递`worker->task`,不支持自定义进程投递以及投递到自定义进程


## 四、数据流程

im-cloud 连接流程图
----
![](./resource/im-cloud-connect.png)

im-cloud 数据流程图
-----
![](./resource/im-cloud-process.png)

im-cloud 业务流程
-----
![](./resource/im-cloudt-task.png)


