<?php

declare(strict_types=1);

namespace Core\Pool;
use Log\Helper\CLog;
use mysql_xdevapi\Exception;
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
     * Minimum active number of connections
     *
     * @var int
     */
    protected $minActive = 5;

    /**
     * Maximum active number of connections
     *
     * @var int
     */
    protected $maxActive = 10;

    /**
     * Maximum waiting time(second), if there is not limit to 0
     *
     * @var float
     */
    protected $maxWaitTime = 0;

    /**
     * @var Channel
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

    /**
     * @param string $name
     * @return mixed
     */
    public function pop(string $name)
    {
        return $this->pools[$name]->pop();
    }

    /**
     * @param string $name
     * @param ConnectionInterface $connection
     */
    public function push(string $name,ConnectionInterface $connection)
    {
        if($this->pools[$name]->length() < $this->maxActive) {
            $this->pools[$name]->push($connection);
        }
    }
    /**
     * @param string $name
     * @param string $option
     * @return mixed
     */
    public  function getPool(string $name,$option = "")
    {
        $channelName = $option.$name;
        //check connection exist
        if(!isset($this->pools[$channelName]) || $this->pools[$channelName] === null){
            $this->pools[$channelName] = new Channel($this->maxActive);
        }

        //check not reach minActive return new create
        if($this->pools[$channelName]->length() < $this->minActive){
            return container()->get($name)->create($option);
        }

        //Pop connection
        $connection = null;
        if(!$this->pools[$channelName]->isEmpty()){
            $connection = $this->pools[$channelName]->pop();
        }

        //return connection
        if($connection !== null){
            return $connection;
        }

        //channel is empty or not reach maxActive return new create
        if($this->pools[$channelName]->length() < $this->maxActive){
            return container()->get($name)->create($option);
        }

        $connection = $this->pools[$channelName]->pop($this->maxWaitTime);
        if($connection === false){
            CLog::error("channel pop timeout name:$name");
            return container()->get($name)->create($option);

        }
        return $connection;
    }

    /**
     * @param PoolConnectionInterface
     */
    public  function releasePool(PoolConnectionInterface $pool,$option = ""){
        if($this->pools[$option.$pool->getName()]->length() < $this->maxActive){
            $this->pools[$option.$pool->getName()]->push($pool);
        }
    }
}
