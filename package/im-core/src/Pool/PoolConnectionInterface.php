<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/21
 * Time: 9:27
 */

namespace Core\Pool;


interface PoolConnectionInterface
{
    /**
     * @param PoolFactory $pool
     * @return mixed
     */
    public function initPool(PoolFactory $pool);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @return ConnectionInterface
     */
    public function createConnection();
}