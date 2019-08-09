<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/22
 * Time: 16:11
 */

namespace Task;


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