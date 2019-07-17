<?php declare(strict_types=1);

namespace Core\Concern;

use function array_merge;
use Stdlib\Helper\ArrayHelper;

/**
 * Trait DataPropertyTrait
 *
 * @since 2.0
 */
trait DataPropertyTrait
{
    /**
     * User custom data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Set value to  context
     * If key is like `a.b`. Equal to set $context['a']['b'] = $value
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value): void
    {
        ArrayHelper::set($this->data, $key, $value);
    }

    /**
     * Get value from context
     * If key is like `a.b`. Equal to get $context['a']['b']
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return ArrayHelper::get($this->data, $key, $default);
    }

    /**
     * Unset key
     *
     * @param string $key
     */
    public function unset(string $key): void
    {
        ArrayHelper::forget($this->data, $key);
    }

    /**
     * Check if an item exists in an array using "dot" notation.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return ArrayHelper::has($this->data, $key);
    }

    /**
     * Set multi value to context
     *
     * @param array $map
     * [key => value]
     */
    public function setMulti(array $map): void
    {
        $this->data = array_merge($this->data, $map);
    }
}
