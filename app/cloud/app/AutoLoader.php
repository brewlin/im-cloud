<?php declare(strict_types=1);


namespace App;


/**
 * Class AutoLoader
 */
class AutoLoader implements \Core\Contract\LoaderInterface
{
    /**
     * @return array
     */
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }
}