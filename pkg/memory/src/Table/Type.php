<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/16 0016
 * Time: 下午 2:50
 */

namespace Memory\Table;

/**
 * Class Type
 * @package Memory\Table
 */
class Type
{
    /**
     * @var string
     */
    const String = \swoole_table::TYPE_STRING;
    /**
     * @var int
     */
    const Int = \swoole_table::TYPE_INT;
    /**
     * @var float
     */
    const Float = \swoole_table::TYPE_FLOAT;

}