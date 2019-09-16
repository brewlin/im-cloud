<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/16 0016
 * Time: 下午 3:02
 */

namespace Memory\Table;

/**
 * Class MemoryTable
 * @package Memory\Table
 * @method bool set(string $key, array $value)
 * @method int incr(string $key, string $column, mixed $incrby = 1)
 * @method int|float decr(string $key, string $column, mixed $decrby = 1)
 * @method array get(string $key, string $field = null)
 * @method bool exist(string $key)
 * @method int count()
 * @method bool del(string $key)
 */
class MemoryTable
{
    private $table = null;

    /**
     * MemoryTable constructor.
     * @param $table \swoole_table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        return $this->table->{$name}(...$arguments);
    }

    /**
     * get all table keys
     * @return array
     */
    public function getKeys():array {
        $keys = [];
        foreach ($this->table as $k => $d){
            $keys[] = $k;
        }
        return !empty($keys)?$keys:[];
    }

}