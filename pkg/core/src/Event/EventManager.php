<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/6
 * Time: 22:10
 */

namespace Core\Event;



use Core\Event\Mapping\Event;

/**
 * Class EventManager
 * @package Core\Event
 */
class EventManager
{
    /**
     * @param string $ns
     * @param Event $obj
     */
    public static function register(string $ns,Event $obj)
    {
        EventContainer::getInstance()->create($ns,$obj->getAlias());
    }

    /**
     * @param string $id
     * @param mixed ...$param
     */
    public static function trigger(string $id,...$param)
    {
        $obj = EventContainer::getInstance()->get($id);
        if(!$obj)return;

        if($obj instanceof EventDispatcherInterface) {
            return $obj->dispatch(...$param);
        }
    }

}