<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/22
 * Time: 15:41
 */

namespace Core\Event;


use Core\Console\Console;
use Psr\Container\ContainerInterface;
use Log\Helper\CLog;

/**
 * Class Event
 * @package Core\Container
 */
class EventContainer implements ContainerInterface
{


    /**
     * @var object
     */
    private $singlePool;

    /**
     * @var object
     */
    private $aliasPool;
    /**
     * @var Container
     */
    public static $instance;

    public static function getInstance()
    {
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;

    }

    /**
     * @param string $id
     * @return object|mixed
     */
    public function get($id)
    {
        if (isset($this->singlePool[$id])) {
            return $this->singlePool[$id];
        }
        if(isset($this->aliasPool[$id])){
            return $this->aliasPool[$id];
        }
        return false;
    }
    public function has($id)
    {
        if (isset($this->singlePool[$id])) {
            return true;
        }
        return false;
    }

    /**
     * @param $id
     * @param string $alias
     */
    public function create($id,$alias = ""){
        if ($this->has($id)) {
            return;
        }
        if(class_exists($id)){
            $this->singlePool[$id] = new $id();
            if($alias)$this->aliasPool[$alias] = $this->singlePool[$id];
        }
        return;
    }

}