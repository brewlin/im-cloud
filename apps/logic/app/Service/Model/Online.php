<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/11
 * Time: 23:05
 */

namespace App\Service\Model;
use Core\Container\Mapping\Bean;

/**
 * Class Online
 * @package App\Service\Model
 * @Bean()
 */
class Online
{
    /**
     * @var string ser
     */
    public $server;
    /**
     * @var array
     */
    public $roomCount;
    /**
     * @var int
     */
    public $updated;

}