<?php declare(strict_types=1);
namespace Core\Concern;

use Core\Container\Container;

/**
 * Class Prototype
 *
 * @since 2.0
 */
trait ContainerTrait
{
    /**
     * Get instance from container
     *
     * @return static
     */
    protected static function __instance()
    {
        return Container::getInstance()->get(static::class);
    }
}
