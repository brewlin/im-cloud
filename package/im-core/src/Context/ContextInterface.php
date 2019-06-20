<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/14 0014
 * Time: 上午 9:07
 */

namespace Core\Context;

/**
 * Class ContextInterface
 *
 * @since 2.0
 */
interface ContextInterface
{
    /**
     * Get value from context
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Set value to context
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value): void;

    /**
     * Set multi value to context
     *
     * @param array $map
     * [key => value]
     */
    public function setMulti(array $map): void;

    /**
     * Clear resource
     */
    public function clear(): void;
}
