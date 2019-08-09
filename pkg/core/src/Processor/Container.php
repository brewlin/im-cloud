<?php

namespace Core\Processor;

use Core\AutoLoader as CoreAutoloader;
use Core\Container\ContainerRegister;
use Core\Contract\Autoloader;

/**
 * Bean processor
 * @since 2.0
 */
class Container extends Processor
{
    /**
     * Handle bean
     *
     * @return bool
     */
    public function handle(): bool
    {
        ContainerRegister::parse();
        return true;
    }


}
