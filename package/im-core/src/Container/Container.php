<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/22
 * Time: 15:41
 */

namespace Core\Container;


use Core\Console\Console;
use Psr\Container\ContainerInterface;
use Log\Helper\CLog;

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

    /**
     * @param string $id
     * @return object|mixed
     */
    public function get($id)
    {
        if (isset($this->singlePool[$id])) {
            return $this->singlePool[$id];
        }
        CLog::error("bean not exist:".$id);
        throw new \Exception("single conainer not exist!".$id);
//        $this->create($id);
//        return $this->singlePool[$id];
    }
    public function has($id)
    {
        if (isset($this->singlePool[$id])) {
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