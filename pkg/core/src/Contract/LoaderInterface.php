<?php declare(strict_types=1);


namespace Core\Contract;

/**
 * Class LoaderInterface
 */
interface LoaderInterface
{
    /**
     * Get namespace and dir
     *
     * @return array
     * [
     *     namespace => dir path
     * ]
     */
    public function getPrefixDirs(): array;

}