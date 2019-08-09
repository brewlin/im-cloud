<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/14 0014
 * Time: 上午 9:07
 */
namespace Core\Context;

use Core\Concern\DataPropertyTrait;

/**
 * Class AbstractContext
 *
 * @since 2.0
 */
abstract class AbstractContext implements ContextInterface
{
    use DataPropertyTrait;

    /**
     * Clear
     */
    public function clear(): void
    {
        $this->data = [];
    }
}
