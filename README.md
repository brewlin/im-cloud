<p align="center">
    <a href="https://github.com/brewlin/im-cloud" target="_blank">
        <img src="https://github.com/brewlin/im-cloud/blob/master/resource/im-logo.png?raw=true" alt="im-cloud"/>
    </a>
</p>
<p>
  <a href="http://docs.huido.site/wiki/im-cloud/index/">
    <img alt="Documentation" src="https://img.shields.io/badge/documentation-yes-brightgreen.svg" target="_blank" />
  </a>
  <a href="https://github.com/brewlin/im-cloud/LICENSE">
    <img alt="License: Apache" src="https://img.shields.io/github/license/brewlin/im-cloud" target="_blank" />
  </a>
 <a href="https://hub.docker.com/r/brewlin/" rel="nofollow">
 <img src="https://camo.githubusercontent.com/db6c049fcef32b9a6850d6d6f1e2e79a7275101e/68747470733a2f2f696d672e736869656c64732e696f2f646f636b65722f6275696c642f73776f66742f616c7068702e737667" alt="Docker Build Status" data-canonical-src="https://img.shields.io/docker/build/swoft/alphp.svg" style="max-width:100%;"></a>
  
 <a href="https://secure.php.net/" rel="nofollow">
 <img src="https://camo.githubusercontent.com/2db74ea6a2c4e00381f6051289dedaa00f9fa38b/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f7068702d2533453d372e312d627269676874677265656e2e7376673f6d61784167653d32353932303030" alt="Php Version" data-canonical-src="https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg?maxAge=2592000" style="max-width:100%;"></a> 
  


<a href="https://github.com/swoole/swoole-src">
<img src="https://camo.githubusercontent.com/936045a17b533972b1519eda85879839d97940ea/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f73776f6f6c652d2533453d342e342e312d627269676874677265656e2e7376673f6d61784167653d32353932303030" alt="Swoole Version" data-canonical-src="https://img.shields.io/badge/swoole-%3E=4.4.1-brightgreen.svg?maxAge=2592000" style="max-width:100%;"></a>
<img alt="GitHub last commit" src="https://img.shields.io/github/last-commit/brewlin/im-cloud">

<img alt="GitHub repo size" src="https://img.shields.io/github/repo-size/brewlin/im-cloud">

</p>

基于原生 swoole 全协程化构建 im-cloud中间件，多节点扩容


## 概述
+ 基于`swoole`原生构建即时推送im分布式服务,不进行业务处理，单独作为中间件使用，可弹性扩充节点增加性能处理,业务demo:(todo)
+ 高性能 水平扩容 分布式服务架构 接入服务治理
+ [`cloud`](appm/cloud) 作为中心服务节点 `grpc-server` 节点，对外可以进行tcp、websocket 客户端进行`长连接`注册,可水平扩容至多个节点 并注册到服务中心 例如`consul`，每个cloud节点维护自己的客户端
+ [`job`](appm/-job) 节点作为消费节点 消费队列数据 然后进行`grpc` 和cloud服务进行通讯 进行 `push` `push room` `broadcast`,作为节点中间件，消费`kafaka`，`rabbitmq。。。`之类,可以通过配置切换消息队列类型
+ [`logic`](appm/logic) 节点 提供rest api接口，作为生产节点 和  grpc客户端,可写入队列作为生产者，也可以扩展自己的业务进行rpc直接调用center中心进行推送,客户端数据缓存至redis中，多个logic节点访问redis数据同步
+ `cloud,job,logic` 等节点可水平扩容多个节点增加并发处理

## appm & apps
+ [`appm`](./appm)多进程版本(`multi process coroutine`) 测试和单元测试中
    - `test version` 
+ [`apps`](./apps)单进程全协程化server版本(`single process coroutine`) 
    - `test version`

## 流程图
im-cloud 连接流程图
----
![](./resource/im-cloud-connect.png)

im-cloud 数据流程图
-----
![](./resource/im-cloud-process.png)

im-cloud 业务流程
-----
![](./resource/im-cloudt-task.png)

## 组件依赖
> 相关组件为纯swoole实现
### @[core](pkg/core) (done) 核心架构
### @[grpc](pkg/grpc) (done) grpc包依赖 grpc-client连接池
### @[discovery](pkg/discovery) (done) 服务发现注册
### @[process](pkg/process)(done) 自定义进程管理器
### @[redis](pkg/redis)(done) redis连接池
### @[queue](pkg/queue)(done amqp,soon kafak) 消息队列连接池
### @[memory](pkg/memory)(done)swoole 相关内存操作封装
### @[task](pkg/task)(done) 异步任务投递组件
### @[cloud](appm/cloud) (test verion)
### @[job](appm/job)   (test version)
### @[logic](appm/logic) (test version)

## 相关文档
- [im-cloud 基于swoole 原生协程构建分布式推送中间件](./docs)
- [im-cloud 分布式中间件的安装部署](./docs)
- [im-cloud <> goim 分布式中间件并发压测对比 ](./docs)
- [im-cloud分布式中间件分析(一)-通讯协议](./docs)
- [im-cloud分布式中间件分析(二)-cloud节点实现](./docs)
- [im-cloud分布式中间件分析(三)-job节点实现](./docs)
- [im-cloud分布式中间件分析(四)-logic节点实现](./docs)


## 📝 License

Copyright © 2019 [brewlin](https://github.com/brewlin).<br />
This project is [Apache2.0](https://github.com/brewlin/im-cloud/LICENSE) licensed.


