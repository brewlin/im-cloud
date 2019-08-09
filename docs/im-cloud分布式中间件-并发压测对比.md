# im-cloud <> goim 分布式中间件并发压测对比 

# 系统环境
>此次测试环境为 window8.1 启动 virtualbox虚拟机部署的ubuntu14
- goim无需担心进程配置，im-cloud测试时候需要根据机器配置做更改`worker进程和task进程最好和cpu核心数`保持一致，太大会使性能大大降低
## 测评对象  `goim` `im-cloud`
- goim (bilibili出品，经过B站生产验证 `百万级消息秒级推`送)
- im-cloud（借鉴goim 使用swoole原生实现  经过自己验证。。。）
## 硬件环境
```
CPU: 4 核cpu
MEM: 2G 内存 
System: Ubunutu 14.04 (64bit)
```
## 软件环境
```
单节点启动
im-cloud: 
    cloud(2 个worker进程               2个子进程) 
    job(  2 个worker进程   2个task进程 1个子进程)  
    logic(2个worker进程  2个task进程   1个子进程)
goim    : 
    comet(单进程)
    job(单进程)
    logic(单进程)
```
## 评测结果
```
c : concurrent 并发请求
n : number     总请求数
-----------------------------------
c:500   |  n:2000 | n:5000 | n:20000
im-cloud:  6300     6082     3815
goim    :  5377     5540     5894  
-----------------------------------
c:1000   |  n:20000
im-cloud:   5014
goim    :   5950   
-----------------------------------
```
## @Concurrent:500 @Number:2000
> `im-cloud` 整体高达6300qps `goim` 整体高达 5300qps
### im-cloud
```
Server Software:        swoole-http-server
Server Hostname:        127.0.0.1
Server Port:            9600

Concurrency Level:      500
Time taken for tests:   0.321 seconds
Complete requests:      2000
Failed requests:        0
Total transferred:      354000 bytes
Total body sent:        374000
HTML transferred:       58000 bytes
Requests per second:    6239.74 [#/sec] (mean)
Time per request:       80.131 [ms] (mean)
Time per request:       0.160 [ms] (mean, across all concurrent requests)
Transfer rate:          1078.55 [Kbytes/sec] received
                        1139.48 kb/s sent
                        2218.03 kb/s total

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:       10   22   5.0     21      35
Processing:     6   33  11.3     30      74
Waiting:        4   25  10.1     24      68
Total:         31   54  11.1     54      98
```
### goim
```

Document Path:          /goim/push/mids?mids=123&operation=1000
Document Length:        23 bytes

Concurrency Level:      500
Time taken for tests:   0.372 seconds
Complete requests:      2000
Failed requests:        0
Total transferred:      292000 bytes
Total body sent:        356000
HTML transferred:       46000 bytes
Requests per second:    5377.91 [#/sec] (mean)
Time per request:       92.973 [ms] (mean)
Time per request:       0.186 [ms] (mean, across all concurrent requests)
Transfer rate:          766.77 [Kbytes/sec] received
                        934.83 kb/s sent
                        1701.60 kb/s total

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0   14  11.8     11      46
Processing:    17   65  27.7     62     149
Waiting:       16   59  24.1     57     136
Total:         31   79  22.0     75     162


```
## @Concurrent:500 @Number:5000
> 5000的请求下 `im-cloud`高达6100qps  
### im-cloud
```
Server Software:        swoole-http-server
Server Hostname:        127.0.0.1
Server Port:            9600
Document Path:          /im/push/mids?mids=123&operation=9&msg=push_mids
Document Length:        29 bytes

Concurrency Level:      500
Time taken for tests:   0.822 seconds
Complete requests:      5000
Failed requests:        0
Total transferred:      885000 bytes
Total body sent:        935000
HTML transferred:       145000 bytes
Requests per second:    6082.98 [#/sec] (mean)
Time per request:       82.196 [ms] (mean)
Time per request:       0.164 [ms] (mean, across all concurrent requests)
Transfer rate:          1051.45 [Kbytes/sec] received
                        1110.86 kb/s sent
                        2162.31 kb/s total

```
### goim
```
Document Path:          /goim/push/mids?mids=123&operation=1000
Document Length:        23 bytes

Concurrency Level:      500
Time taken for tests:   0.907 seconds
Complete requests:      5000
Failed requests:        0
Total transferred:      730000 bytes
Total body sent:        890000
HTML transferred:       115000 bytes
Requests per second:    5514.84 [#/sec] (mean)
Time per request:       90.664 [ms] (mean)
Time per request:       0.181 [ms] (mean, across all concurrent requests)
Transfer rate:          786.30 [Kbytes/sec] received
                        958.63 kb/s sent
                        1744.93 kb/s total

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0   18  13.4     16      54
Processing:    20   64  25.0     63     143
Waiting:       17   55  20.5     54     117
Total:         35   82  21.3     80     161

```

## @Concurrent:500 @Number:20000
>请求20000 并发500 
### goim
```
Document Path:          /goim/push/mids?mids=123&operation=1000
Document Length:        23 bytes

Concurrency Level:      500
Time taken for tests:   3.393 seconds
Complete requests:      20000
Failed requests:        0
Total transferred:      2920000 bytes
Total body sent:        3560000
HTML transferred:       460000 bytes
Requests per second:    5894.30 [#/sec] (mean)
Time per request:       84.828 [ms] (mean)
Time per request:       0.170 [ms] (mean, across all concurrent requests)
Transfer rate:          840.40 [Kbytes/sec] received
                        1024.60 kb/s sent
                        1864.99 kb/s total

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0   30 129.4     10    1025
Processing:    10   50  19.2     47     128
Waiting:        6   42  16.1     40     125
Total:         16   80 131.8     60    1100

```
### im-cloud
```
Server Software:        swoole-http-server
Server Hostname:        127.0.0.1
Server Port:            9600
Document Path:          /im/push/mids?mids=123&operation=9&msg=push_mids
Document Length:        29 bytes

Concurrency Level:      500
Time taken for tests:   5.242 seconds
Complete requests:      20000
Failed requests:        0
Total transferred:      3540000 bytes
Total body sent:        3740000
HTML transferred:       580000 bytes
Requests per second:    3815.33 [#/sec] (mean)
Time per request:       131.050 [ms] (mean)
Time per request:       0.262 [ms] (mean, across all concurrent requests)
Transfer rate:          659.49 [Kbytes/sec] received
                        696.75 kb/s sent
                        1356.23 kb/s total

```

## @Concurrent:1000 @Number:20000
>请求20000 并发1000
### goim
```
Document Path:          /goim/push/mids?mids=123&operation=1000
Document Length:        23 bytes

Concurrency Level:      1000
Time taken for tests:   3.361 seconds
Complete requests:      20000
Failed requests:        0
Total transferred:      2920000 bytes
Total body sent:        3560000
HTML transferred:       460000 bytes
Requests per second:    5950.20 [#/sec] (mean)
Time per request:       168.061 [ms] (mean)
Time per request:       0.168 [ms] (mean, across all concurrent requests)
Transfer rate:          848.37 [Kbytes/sec] received
                        1034.31 kb/s sent
                        1882.68 kb/s total

```
### im-cloud
```
Server Software:        swoole-http-server
Server Hostname:        127.0.0.1
Server Port:            9600

Document Path:          /im/push/mids?mids=123&operation=9&msg=push_mids
Document Length:        29 bytes

Concurrency Level:      500
Time taken for tests:   3.988 seconds
Complete requests:      20000
Failed requests:        0
Total transferred:      3540000 bytes
Total body sent:        3740000
HTML transferred:       580000 bytes
Requests per second:    5014.66 [#/sec] (mean)
Time per request:       99.708 [ms] (mean)
Time per request:       0.199 [ms] (mean, across all concurrent requests)
Transfer rate:          866.79 [Kbytes/sec] received
                        915.76 kb/s sent
                        1782.56 kb/s total

```