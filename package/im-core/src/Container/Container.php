<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/22
 * Time: 15:41
 */

namespace Core\Container;


use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var object
     */
    private $singlePool;
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
    public function get($id)
    {
        if (isset($this->singletonPool[$id])) {
            return $this->singlePool[$id];
        }
        $this->create($id);
        return $this->singlePool[$id];
    }
    public function has($id)
    {
        if (isset($this->singletonPool[$id])) {
            return true;
        }
        return false;
    }
    public function create($id){
        if ($this->has($id)) {
            return;
        }
        if(class_exists($id)){
            $this->singlePool[$id] = new $id();
        }
        return;
    }

}