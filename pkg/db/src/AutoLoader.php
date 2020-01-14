<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/14
 * Time: 16:11
 */

namespace Db;

class AutoLoader implements \Core\Contract\LoaderInterface
{
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
        // TODO: Implement getPrefixDirs() method.
    }

}