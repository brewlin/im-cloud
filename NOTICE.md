# notices

## appm | app s
+ [`appm`](./appm)多进程协程版本(`multi process coroutine`)
+ [`apps`](./apps)单进程全协程化server版本(`single process coroutine`) 采用swoole协程化server

## @dev 本地开发
本地开发环境需要 引入 swoole助手函数包方便编译器提示，推荐`swoft/swoole-ide-helper`

## @debug 调试
ab 压测的时候需要注意，如果开启日志且在终端打印，那么并发的性能会降低很多

开启了日志记录到文件也会影响并发性能，因为在每个请求期间会有一个协程去读写文件，生产项目可以使用独立队列去专门记录日志

## @option 命令行可选项
- `--start` 启动服务
- `--restart` 重启服务，默认守护进程
- `--stop` kill - 15 关闭服务
- `--log=true` 是否开启日志记录  需要配置 `env` 里 log_type 日志记录类型，`console`打印到终端  `file` 打印到文件 默认 runtime/logs
- `--debug` 开启debug日志调试，也就是在代码中日志级别为debug的日志