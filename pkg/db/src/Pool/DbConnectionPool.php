<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/14 0014
 * Time: 下午 5:54
 */

namespace Db\Pool;


use Core\Container\Mapping\Bean;
use Core\Pool\PoolFactory;
use Db\Connector\MysqlConnector;
use Hyperf\Database\MySqlConnection;

/**
 * Class DbConnectionPool
 * @package Db\Pool
 * @Bean()
 */
class DbConnectionPool
{
    /**
     * @var array $config
     */
    protected $config;
    /**
     * @var MysqlConnector
     */
    protected $connection;
    /**
     * @var
     */
    protected $name;

    /**
     * @param $config
     * @return $this
     */
    public function init($config)
    {
        $this->config = $config;
        $this->name = DbConnectionPool::class;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getConfig(){
        return $this->config;
    }

    /**
     * @return \Redis
     */
    public function createConnection(): MySqlConnection
    {
        if(!$this->connection)
            $this->connection = (new MysqlConnector())->connect($this->config,$this->config['option']??[]);
        return $this->connection;
    }
    public function release(DbConnectionPool $pool){
        /** @var PoolFactory $pool */
        $poolFactory = container()->get(PoolFactory::class);
        $poolFactory->releasePool($pool);
    }

    /**
     * @return DbConnectionPool
     */
    public function create($options = ""){
        $config = config("db");
        $obj = new DbConnectionPool();

        return  $obj->init($config);
    }
}