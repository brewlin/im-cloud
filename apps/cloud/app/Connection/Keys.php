<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/19 0019
 * Time: 下午 3:44
 */

namespace App\Connection;

use Core\Container\Mapping\Bean;

/**
 * Class Keys
 * @package App\Connection
 * @Bean()
 */
class Keys
{
    /**
     * @var array
     */
    private $keys = [];

    /**
     * @return int|bool
     */
    public function getFd(string $key)
    {
        if(!isset($this->keys[$key])){
            return false;
        }
        return $this->keys[$key];
    }

    /**
     * @param array $keys
     */
    public function setFd(string $key,int $fd)
    {
        $this->keys[$key] = $fd;
    }

    /**
     * @param string $key
     */
    public function delFd(string $key)
    {
        unset($this->keys[$key]);
    }

}