<?php

declare(strict_types=1);

namespace Core\Pool;
use \Swoole\Coroutine\Channel;
use Core\Container\Mapping\Bean;

/**
 * Class PoolFactory
 * @package ImQueue\Pool
 * @Bean()
 */
class PoolFactory
{
    /**
     * @var PoolConnectionInterface
     */
    private $pools = [];

    /**
     * @param string $name
     * @return bool
     */
    public function check(string $name){
        if(!isset($this->pools[$name])){
            return false;
        }
        return true;
    }
    public function pop(string $name)
    {
        return $this->pools[$name]->pop();
    }
    public function push(string $name,ConnectionInterface $connection)
    {
        $this->pools[$name]->push($connection);
    }
    /**
     * @param string $name
     * @return PoolConnectionInterface
     */
    public  function getPool(string $name)
    {
        if (isset($this->pools[$name])) {
            return $this->pools[$name]->pop();
        }
        container()->get($name)->initPool($this);
        return $this->pools[$name]->pop();
    }

    /**
     * @param PoolConnectionInterface
     */
    public  function releasePool(PoolConnectionInterface $pool){
        if (isset($this->pools[$pool->getName()])) {
            return $this->pools[$pool->getName()]->push($pool);
        }
    }

    /**
     * @param $name
     * @param Channel $pool
     */
    public function registerPool($name,Channel $pool)
    {
        if (!isset($this->pools[$name])) {
            $this->pools[$name] = $pool;
        }
    }
}
