<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/18
 * Time: 22:34
 */

namespace App\Tcp;
use Core\Container\Mapping\Bean;

/**
 * Class Bucket
 * @package App\Tcp
 * @Bean()
 */
class Bucket
{
    /**
     * @var Connection
     */
    private $conn = null;

    /**
     * @param Connection $conn
     */
    public function put(Connection $conn)
    {
        $this->conn[$conn->getFd()] = $conn;
    }

    /**
     * @param Connection $conn
     */
    public function del(Connection $conn)
    {
        unset($this->conn[$conn->getFd()]);
    }

}