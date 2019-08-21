# im-cloud 分布式中间件的安装部署
>github:http://github.com/brewlin/im-cloud
- [im-cloud 基于swoole 原生协程构建分布式推送中间件](./docs)
- `im-cloud 分布式中间件的安装部署`
- [im-cloud <> goim 分布式中间件并发压测对比 ](./docs)
- [im-cloud分布式中间件分析(一)-通讯协议](./docs)
- [im-cloud分布式中间件分析(二)-cloud节点实现](./docs)
- [im-cloud分布式中间件分析(三)-job节点实现](./docs)
- [im-cloud分布式中间件分析(四)-logic节点实现](./docs)

安装方式主要提供 `docker单节点部署` `docker-compose自动化编排部署` `手动部署` 三种方式部署环境

## 一、docker部署
>基础镜像足够小 不用担心  `base image+php7.2+swoole 4 才75M`

docker-compose networknamespace 为host模式，所以需要注意本地端口冲突的问题,也可以根据自己的环境更改compose.yml配置
### 1.docker 单独部署
- 镜像
    - consul
    - redis
    - brewlin/cloud
    - brewlin/job
    - brewlin/logic
- 启动consul
```
docker run --network host consul
```
- 启动redis
```
docker run --network host redis
```
- 启动cloud节点
```
docker run --network host brewlin/cloud
```
- 启动job节点
```
docker run --network host  brewlin/job
```
- 启动logic节点
```
docker run --network host  brewlin/logic
```

### 2.docker-compose 编排服务
```
git clone http://github.com/brewlin/im-cloud
cd im-cloud
docker-compose up
```
## 二、手动部署
### 环境要求
- swoole 4 +
- php 7.2 +
- console
- rabbitmq
- redis
### 1.安装依赖
make脚本使用composer自动install相关组件
```
cd path/im-cloud
make install
```
### 2.启动cloud节点
```
cd path/im-cloud/app/cloud
php bin/app
```
### 3.启动logic节点
```
cd path/im-cloud/app/logic;
php bin/app
```
### 4.启动job节点
```
cd path/im-cloud/app/job;
php bin/app
```
### 5.安装启动consul
1、登录官网进行下载，下载地址
```shell
wget https://releases.hashicorp.com/consul/1.2.1/consul_1.2.1_linux_amd64.zip
unzip consul_1.2.1_linux_amd64.zip
```
2、设置环境变量，如果不设置可以直接把consul执行文件移动到/usr/bin目录下
```shell
mv consul /usr/bin
```
3、 单机配置、这种方式适合用于搭建服务调试使用
```
consul agent -bootstrap-expect 1 -server -data-dir /data/consul -node=cloud -bind=127.0.0.1 -config-dir /etc/consul.d -enable-script-checks=true -datacenter=dc1 -client=0.0.0.0 -ui
```
可以通过 http://192.168.1.100:8500 查看服务信息
### 6.安装rabbitmq
- 1.添加新的源

```
echo 'deb http://www.rabbitmq.com/debian/ testing main' |
     sudo tee /etc/apt/sources.list.d/rabbitmq.list
```
- 2.下载公钥

```
wget -O- https://www.rabbitmq.com/rabbitmq-release-signing-key.asc |
     sudo apt-key add -
```
- 3.更新 & 安装

```
sudo apt-get update
sudo apt-get install rabbitmq-server
```
- 4.修改配置文件

```
cd /etc/rabbitmq/
新建文件
touch rabbitmq.config

在配置文件中加入

[{rabbit, [{loopback_users, []}]}].
```


- 5.状态管理
```
rabbitmqctl status 可查看rabbitmq的状态
```

- 6.开启web管理界面
```shell
# 开启rabbitmq web界面
Host]# rabbitmq-plugins enable rabbitmq_management
    The following plugins have been enabled:
        rabbitmq_web_dispatch
        rabbitmq_management_agent
        rabbitmq_management
```

- 7.创建用户
```
Host]# rabbitmqctl add_user xiaodo xiaodo
    Creating user "admin"
Host]# rabbitmqctl set_user_tags xiaodo administrator
    Setting tags for user "admin" to [administrator]
```
## 三、测试
- 1.使用js sdk 提供的demo 注册到cloud
- 2.post `http://host:9600/im/push/mids?mids=123&operation=9&msg=pushtest` 进行单点推送