<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/19 0019
 * Time: 下午 3:39
 */

namespace App\Connection;

use Core\Container\Mapping\Bean;

/**
 * Class Counts
 * @package App\Connection
 * @Bean()
 */
class Counts
{
    /**
     * @var array
     */
    private $ipCounts = [];

    /**
     * @return void
     */
    public function incr():void
    {
        //ipcount ++
        if(!isset($this->ipCounts[env("APP_HOST","127.0.0.1")])){
            $this->ipCounts[env("APP_HOST","127.0.0.1")] = 0;
        }
        $this->ipCounts[env("APP_HOST","127.0.0.1")] ++;
    }

    /**
     * @return void
     */
    public function decr():void
    {
        $this->ipCounts[env("APP_HOST","127.0.0.1")] --;
    }

}