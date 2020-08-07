<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/14 0014
 * Time: 下午 4:34
 */

namespace Database\Connector;


use Core\Container\Mapping\Bean;
use Database\Event\DBDispatcherEvent;
use Hyperf\Database\MySqlConnection;

/**
 * Class MysqlConnector
 * @package Db\Connector
 * @Bean()
 */
class MysqlConnector
{
    /**
     * driver
     * @var string
     */
    protected $driver = 'mysql';

    /**
     * host
     * @var string
     */
    protected $host = "127.0.0.1";

    /**
     * port
     * @var int
     */
    protected $port = 3306;

    /**
     * user-name
     * @var string
     */
    protected $username = "test";

    /**
     * password
     * @var string
     */
    protected $password = "test";

    /**
     * dsn
     * @var string
     */
    protected $dsn = "%s:host=%s:%s;dbname=%s";

    /**
     * dbname
     * @var string
     */
    protected $database = "test";

    /**
     * prefix
     * @var string
     */
    protected $prefix = '';

    /**
     * option
     * @var array
     */
    protected $option = [
        \PDO::ATTR_CASE => \PDO::CASE_NATURAL
    ];

    /**
     * @param array $config
     * @param array $option
     */
    public function connect(array $config, array $option): MySqlConnection
    {
        $client = $this->createpdo($config,$option);

        return $this->establishConnection($client, $config);
    }

    /**
     * create pdo
     * @param array $config
     * @param array $option
     * @return \PDO
     */
    public function createpdo(array $config,array $option):\PDO
    {
        if(!empty($config['driver'])){
            $this->driver = $config['driver'];
        }
        if(!empty($config['username'])){
            $this->username = $config['username'];
        }
        if(!empty(($config['password']))){
            $this->password = $config['password'];
        }
        if(!empty($option)){
            $this->option = $option;
        }
        if(!empty($config['database'])){
            $this->database = $config['database'];
        }
        if(!empty($config['prefix'])){
            $this->prefix = $config['prefix'];
        }
        if(!empty($config['port'])){
            $this->port = $config['port'];
        }
        if(!empty($config['host'])){
            $this->host = $config['host'];
        }
        $this->dsn = sprintf($this->dsn,$this->driver,$this->host,$this->port,$this->database);
        try{
            $pdo = new \PDO($this->dsn,$this->username,$this->password,$this->option);
        }catch (\Throwable $e){
            throw new \Exception($e->getMessage());
        }
        return $pdo;

    }

    /**
     * Establish a connection with the Redis host.
     *
     * @param MySqlConnection $client
     * @param array  $config
     *
     * @return MySqlConnection
     */
    protected function establishConnection(\PDO $client, array $config): MySqlConnection
    {
        try{
            $client = new MySqlConnection($client,$this->database,$this->prefix,$config);
            //register event
            $client->setEventDispatcher(\bean(DBDispatcherEvent::class));
        }catch (\Throwable $e){
            throw new \Exception(
                sprintf('mysql connect error(%s)', $e->getMessage())
            );
        }
        return $client;

    }
}