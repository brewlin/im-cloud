<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/17 0017
 * Time: 上午 11:40
 */

namespace Core\Event;


/**
 * Interface EventDispatcherInterface
 * @package Core\Event
 */
interface EventDispatcherInterface
{
    /**
     * @param mixed ...$param
     * @return mixed
     */
    public function dispatch(...$param);

}