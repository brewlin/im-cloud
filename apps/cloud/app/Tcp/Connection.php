<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/18
 * Time: 22:39
 */

namespace App\Tcp;
use Swoole\Coroutine\Server\Connection as Con;

/**
 * Class Connection
 * @package App\Tcp
 * @method int|false send(string $data,double $timeout = -1)
 * @method bool close()
 */
class Connection
{
    /**
     * @var Con
     */
    private $con;

    /**
     * Connection constructor.
     * @param Con $con
     */
    public function __construct(Con $con)
    {
        $this->con = $con;
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
     * @return int
     */
    public function getFd():int
    {
        return $this->socket->fd;
    }


}