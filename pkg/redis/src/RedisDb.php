<?php
/**
 * @author brewlin
 * @date 19.7.3
 * @time 17:11
 */
namespace ImRedis;
use Core\Container\Mapping\Bean;

/**
 * Class RedisDb
 * @Bean()
 */
class RedisDb
{
    /**
     * Php redis
     */
    const PHP_REDIS = 'phpredis';

    /**
     * P redis
     */
    const P_REDIS = 'predis';

    /**
     * @var string
     */
    private $driver = self::PHP_REDIS;

    /**
     * @var string
     */
    private $host = '127.0.0.1';

    /**
     * @var int
     */
    private $port = 6379;

    /**
     * @var int
     */
    private $database = 0;

    /**
     * @var string
     */
    private $password = '';

    /**
     * @var float
     */
    private $timeout = 0.0;

    /**
     * @var int
     */
    private $retryInterval = 10;

    /**
     * @var int
     */
    private $readTimeout = 0;

    /**
     * Set client option.
     *
     * @var array
     *
     * @example
     * [
     *     'serializer ' => Redis::SERIALIZER_PHP/Redis::SERIALIZER_NONE/Redis::SERIALIZER_IGBINARY,
     *     'prefix' => 'xxx',
     * ]
     */
    private $option = [];

    /**
     * @var array
     *
     * @example
     * [
     *     [
     *         'host' => '127.0.0.1',
     *         'port' => 6379,
     *         'database' => 1,
     *         'password' => 'xxx',
     *         'prefix' => 'xxx',
     *         'read_timeout' => 1,
     *     ],
     *     ...
     * ]
     */
    private $clusters = [];

    /**
     * @var array
     */
    private $connectors = [];
    /**
     * @var array
     */
    private $config = [];

    /**
     * @var array
     */
    protected $connections = [];

    public  function init(array $redisConfig)
    {
        $this->setConfig($redisConfig);
        return $this;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        if(!empty($config["host"])){
            $this->host = $config["host"];
        }
        if(!empty($config["port"])) {
            $this->port = $config["port"];
        }
        if(!empty($config["database"])) {
            $this->database = $config["database"];
        }

    }

    public function getConfig():array
    {
        return $this->config;

    }
    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return int
     */
    public function getDatabase(): int
    {
        return $this->database;
    }

    /**
     * @return float
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getRetryInterval(): int
    {
        return $this->retryInterval;
    }

    /**
     * @return int
     */
    public function getReadTimeout(): int
    {
        return $this->readTimeout;
    }

    /**
     * @return array
     */
    public function getOption(): array
    {
        return $this->option;
    }

    /**
     * @return array
     */
    public function getClusters(): array
    {
        return $this->clusters;
    }
}
