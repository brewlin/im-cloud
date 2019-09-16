memory composer包 
==============
概述
=======
## memory
```php
封装共享内存相关的组件  基于 swoole/memory/table
use \Memory\Table;
use \Memory\Table\Type;
$colum = [
    "Addr" => [Type::String,10]k
]
$table = Table::create(1000,$colum);

//methods
$table->get($key):array
$table->del($key):bool
$table->set($key ,[]):bool;
$table->getkeys():array
$table->exist($key):bool
$table->del($key):bool
$table->incr($key):bool
$table->decr($key):bool
```

