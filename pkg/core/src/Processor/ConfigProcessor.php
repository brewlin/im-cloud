<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/14 0014
 * Time: 下午 3:24
 */

namespace Core\Processor;


use Core\Config\Config;

class ConfigProcessor extends Processor
{
    public function handle(): bool
    {
        // TODO: Implement handle() method.
        define("APP_NAME",(string)getenv("APP_NAME","im-undifined-node"));
        \Core\Container\Container::getInstance()->create(Config::class);
        return true;
    }


}