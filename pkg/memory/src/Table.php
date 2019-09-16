<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/16 0016
 * Time: 下午 2:49
 */

namespace Memory;

use Core\Container\Mapping\Bean;
use Log\Helper\Log;
use Memory\Table\MemoryTable;

/**
 * Class Table
 * @package Memory\Table
 * @Bean()
 */
class Table
{
    const DefaultSize = 1000 * 2;
    /**
     * @param int $size
     * @param array $column
     * {
            string => [
                   key => len,
                   key2 => len2,
     *      ]
     * }
     * @throws \Exception
     * @return MemoryTable
     */
    public static function create(int $size,array $column)
    {
        if(empty($column)){
            Log::error("create memory table column is null");
            throw new \Exception("create memory table column is null");
        }
        if(empty($size))$size = self::DefaultSize;

        $table = new \swoole_table($size);
        foreach ($column as $key => $types){
            $table->column($key,$types[0],$types[1]);
        }
        $table->create();
        return (new MemoryTable($table));

    }

}