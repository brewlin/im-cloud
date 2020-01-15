<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/14 0014
 * Time: 下午 4:29
 */

namespace Database;

use Core\Context\Context;
use Core\Pool\PoolFactory;
use Database\Pool\DbConnectionPool;

use Hyperf\Database\MySqlConnection;
use Hyperf\Database\Query\Builder;
use Throwable;

/**
 * Class Db
 * @package Db
 * @method static void beginTransaction()
 * @method static void commit()
 * @method static void rollBack($toLevel = null)
 * @method static Builder table($table = "test")
 */
class Db
{
    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $arguments)
    {
        /** @var DbConnectionPool $pool */
        $pool = self::connection();
        /** @var MySqlConnection $connection */
        $connection = $pool->createConnection();
        Context::addPool($pool);
        $res = $connection->{$method}(...$arguments);
        return $res;

    }
    /**
     * @param string $pool
     *
     */
    private static function connection()
    {
        try {
            /** @var DbConnectionPool $pool */
            $pool = bean(PoolFactory::class)->getPool(DbConnectionPool::class);

        } catch (Throwable $e) {
            throw new \Exception(
                sprintf('Pool error is %s file=%s line=%d', $e->getMessage(), $e->getFile(), $e->getLine())
            );
        }
        return $pool;
    }

}