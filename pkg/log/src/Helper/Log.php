<?php declare(strict_types=1);


namespace Log\Helper;


use Core\Container\Mapping\Bean;
use ReflectionException;
use function sprintf;
use Log\Logger;

/**
 * Class Log
 * @package Log\Helper
 */
class Log
{
    /**
     * @var string stdout to console
     */
    const TypeConsole = "console";
    /**
     * @var  string log to file
     */
    const TypeFile = "file";

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     * @throws ReflectionException
     */
    public static function emergency(string $message, ...$params)
    {
        if(!env("START_LOG",false))return;
        if(env("LOG_TYPE") == self::TypeConsole){
            return CLog::warning($message,...$params);
        }
        return self::getLogger()->emergency(sprintf($message, ...$params));
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     * @throws ReflectionException
     */
    public static function debug(string $message, ...$params)
    {
        if(!env("START_LOG",false))return;
        if (env("APP_DEBUG")) {
            if(env("LOG_TYPE") == self::TypeConsole){
                return CLog::debug($message,...$params);
            }
            return self::getLogger()->debug(sprintf($message, ...$params));
        }

        return true;
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     * @throws ReflectionException
     */
    public static function alert(string $message, ...$params)
    {
        if(!env("START_LOG",false))return;
        if(env("LOG_TYPE") == self::TypeConsole){
            return CLog::warning($message,...$params);
        }
        return self::getLogger()->alert(sprintf($message, ...$params));
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     */
    public static function info(string $message, ...$params)
    {
        if(!env("START_LOG",false))return;
        if(env("LOG_TYPE") == self::TypeConsole){
            return CLog::info($message,...$params);
        }
        return self::getLogger()->info(sprintf($message, ...$params));
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     */
    public static function warning(string $message, ...$params)
    {
        if(!env("START_LOG",false))return;
        if(env("LOG_TYPE") == self::TypeConsole){
            return CLog::warning($message,...$params);
        }
        return self::getLogger()->warning(sprintf($message, ...$params));
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     */
    public static function error(string $message, ...$params)
    {
        if(!env("START_LOG",false))return;
        if(env("LOG_TYPE") == self::TypeConsole){
            return CLog::error($message,...$params);
        }
        return self::getLogger()->error(sprintf($message, ...$params));
    }


    /**
     * Push log
     *
     * @param string $key
     * @param mixed  $val
     *
     */
    public static function pushLog(string $key, $val)
    {
        self::getLogger()->pushLog($key, $val);
    }

    /**
     * Profile start
     *
     * @param string $name
     * @param array  $params
     *
     */
    public static function profileStart(string $name, ...$params): void
    {
        self::getLogger()->profileStart(sprintf($name, ...$params));
    }

    /**
     * @param string   $name
     * @param int      $hit
     * @param int|null $total
     *
     */
    public static function counting(string $name, int $hit, int $total = null): void
    {
        self::getLogger()->counting($name, $hit, $total);
    }

    /**
     * Profile end
     *
     * @param string $name
     * @param array  $params
     */
    public static function profileEnd(string $name, ...$params): void
    {
        self::getLogger()->profileEnd(sprintf($name, ...$params));
    }

    /**
     * @return Logger
     */
    public static function getLogger(): Logger
    {
        return container()->get(Logger::class);
    }
}
