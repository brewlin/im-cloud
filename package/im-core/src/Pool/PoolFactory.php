<?php

declare(strict_types=1);

namespace Core\Pool;
use Co\Channel;
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
