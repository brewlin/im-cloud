im-cloud process composer包 
==============
概述
=======
+ 自定义进程，并注册到swoole管理上，随着swoole启动而启动，生命周期交由swoole manager process 管理
## `register` 注册自定义管理进程
```php
//config/process.php
ProcessManager::register("test",new Test);

```

