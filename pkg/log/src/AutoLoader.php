<?php declare(strict_types=1);

namespace Log;

use Core\Contract\LoaderInterface;
use function dirname;


/**
 * Class AutoLoader
 *
 */
class AutoLoader implements LoaderInterface
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
