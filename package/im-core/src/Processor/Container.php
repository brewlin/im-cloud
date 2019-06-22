<?php

namespace Core\Processor;

use Core\Autoloader as CoreAutoloader;
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
        $autoloader = $this->defaultAutoload();
        foreach ($autoloader as $loader){
            if($loader instanceof Autoloader){
                $loader->handler();
            }
        }
        return true;
    }

    /**
     * @return array[object]
     */
    public function defaultAutoload(){
        return [
            new CoreAutoloader(),
            new \Discovery\AutoLoader()
        ];
    }

}
