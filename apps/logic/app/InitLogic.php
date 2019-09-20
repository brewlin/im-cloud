<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/23
 * Time: 18:30
 */

namespace App;


use App\Lib\Logic;
use Core\Container\Mapping\Bean;

/**
 * @package App\Process
 * @Bean()
 */
class InitLogic
{

    /**
     * loadonline
     */
    public function run()
    {

        $scheduler = new Coroutine\Scheduler;
        $scheduler->add([Logic::class,"loadOnline"]);
        $scheduler->start();
    }

}