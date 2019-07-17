im-cloud 底层基础库，composer包 
==============
概述
=======
+ 作为`cloud`,`job`,`logic` 等网络io节点的基础库，封装了swoole的多种协议处理，方便业务开发，也可以根据业务自定义扩展，轻量级实现，高可扩展基础库，这也是没有选择现在主流swoole的一些框架的原因，很多特性不需要，也不方便自己进行底层扩展
+ 主要是作为全协程业务基础库，提供业务cloud,logic 节点支撑，会不断重构达到性能极致
## 目录结构
```
- src
----- Concern  公用trait 类
----- Console  打印终端字体
----- Context  封装的协程上下文接口 和 公用实现
----- Contract 公用interface接口
----- Helper   全局助手 如 request() response()等 获取协程上下文
----- Http     http 和http2 的实现 包括swoole注册启动以及http生命周期的管理，
----- Listener 封装默认的监听swoole 事件监听器
----- Log      使用   log 依赖进行日志打印
----- Processor 程序启动 默认加载的模块
----- server    管理swoole启动 http  tcp websocket 等
----- Swoole    封装swoole接口
----- App.php   主core app
----- Cloud.php 挂载全状态
----- Co.php    封装swoole/Cortinue 协程
----- WaitGroup.php 协程同状态
```

