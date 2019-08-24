<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/14 0014
 * Time: 下午 3:24
 */

namespace Core\Processor;


use Core\Config\Config;
use Core\Console\Console;

/**
 * Class ConsoleProcessor
 * @package Core\Processor
 */
class ConsoleProcessor extends Processor
{
    /**
     * @return bool
     */
    public function handle(): bool
    {
        //处理命令行参数
        /** @var Console $cli */
//        $cli =  bean(Console::class);
        return true;
    }


}