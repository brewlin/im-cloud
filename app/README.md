应用节点
==============

## @[cloud](./cloud)
> 注册中心节点
```php
cd cloud
composer install
```
## 进度
- 完成grpc接口
- 完成tcp  websocket 注册链接
- 完成主流程

## @[job](./job)
> 消费中心节点
```php
cd job
composer install
```
### 进度
- 完成了队列的消费 多进程消费
- 完成了grpc cloud的调用

## @[logic](./logic)
> 业务中心节点
```php
cd logic
composer install
```
### 进度
- 完成了http api
- 完成了grpc api
- 完成了注册发现与服务调用
- 完成了队列的生产 使用rabbitmq

