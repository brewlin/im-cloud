## Grpc proto build


```go
//将固定版本的protoco 移到当前目录
$> mv {version}/* .
//执行脚本自动构建
$> sh gen.sh

```

## @v1.0.0
- cloud 节点支持 grpc推送
- logic 节点支持 客户端注册
## @v2.0.0
- `新支持` logic节点 提供grpc 推送