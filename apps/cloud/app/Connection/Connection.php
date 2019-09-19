<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/18
 * Time: 22:39
 */

namespace App\Connection;
use Swoole\Coroutine\Server\Connection as Con;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;

/**
 * Class Connection
 * @package App\Tcp
 * //tcp
 * @method int|bool send(string $data,double $timeout = -1)
 * @method string|Frame recv()
 *
 * //websocket
 * @method bool push(mixed $data,int $opcode = 1,bool $finish = true)
 * @method bool upgrade() 升级为ws连接
 */
class Connection
{
    /**
     * @var Con | Response
     */
    private $con;

    /**
     * connection type
     * @var int
     */

    private $type = self::Websocket;

    /**
     * tcp type
     * @var int
     */
    const Tcp = 1;

    /**
     * websocket type
     * @var int
     */
    const Websocket = 2;

    /**
     * @var string
     */
    private $key = "";

    /**
     * @var string
     */
    private $mid = "";

    /**
     * Connection constructor.
     * @param Con|Response $con
     */
    public function __construct($con,int $type)
    {
        $this->type = $type;
        $this->con = $con;
    }
    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        return $this->con->{$name}(...$arguments);
    }

    /**
     * @return int
     */
    public function getFd():int
    {
        //abort this func
        return $this->con->socket->fd;
    }

    /**
     * close the connection
     */
    public function close():void
    {
        $this->con->socket->close();
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getMid(): string
    {
        return $this->mid;
    }

    /**
     * @param string $mid
     */
    public function setMid(string $mid)
    {
        $this->mid = $mid;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isTcp():bool
    {
        return $this->getType() == self::Tcp;
    }

    /**
     * @return bool
     */
    public function isWs():bool
    {
        return $this->getType() == self::Websocket;
    }


}