# im-cloud 分布式中间件的安装部署

## 一、docker部署
>基础镜像足够小 不用担心  `base image+php7.2+swoole 4 才75M`

## 二、手动部署

### 1.docker 单独部署

### 2.docker-compose 编排服务

### 环境要求
- swoole 4 +
- php 7.2 +

### 1.安装依赖
> make脚本使用composer自动install相关组件
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
ab -c 500 -n 2000 -p p 'http://127.0.0.1:9600/im/push/mids?mids=123&operation=9&msg=push_mids'
