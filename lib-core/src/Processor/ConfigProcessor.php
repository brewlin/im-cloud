<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/14 0014
 * Time: 下午 3:24
 */

namespace Core\Processor;


class ConfigProcessor extends Processor
{
    public function handle(): bool
    {
        // TODO: Implement handle() method.
        define('APP_DEBUG', (int)getenv('APP_DEBUG', 0));
        define('SWOFT_DEBUG', (int)getenv('App_DEBUG', 0));
        return true;
    }

}