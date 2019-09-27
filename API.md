# api docs

## logic-m
logic 节点提供了 api接口进行推送

### @mids 
根据业务mid（memberid）进行推送
url:{host}/im/push/mids

[POST] params:
```
{
    mids:[...],//arr
    operation:9,//push
    msg:"test",//msg
}
```

### @keys 
根据用户唯一key进行推送
url:{host}/im/push/keys

[POST] params:
```
{
    keys:[...],//arr
    operation:9,//push
    msg:"test",//msg
}
```

### @room
广播房间
url:{host}/im/push/room

[POST] params:
```
{
    operation:9,//push
    msg:"test",//msg
    type:"live://",
    room:"1000",
}
```
### @broadcast
广播所用用户
url:{host}/im/push/all

[POST] params:
```
{
    operation:9,//push
    msg:"test",//msg
}
```