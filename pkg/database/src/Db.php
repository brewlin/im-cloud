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
 */
class Db
{
    /**
     * @param string $pool
     *
     */
    public static function connection()
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

    /**
     * @param string $method
     * @param array  $argument
     * @return mixed
     */
    public static function table($table = "test"):Builder
    {
        /** @var DbConnectionPool $pool */
        $pool = self::connection();
        /** @var MySqlConnection $connection */
        $connection = $pool->createConnection();
        Context::addPool($pool);
        return $connection->table($table);

    }
}