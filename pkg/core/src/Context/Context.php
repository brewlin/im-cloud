<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/14 0014
 * Time: 上午 9:07
 */
namespace Core\Context;

use Core\Http\HttpContext;
use ReflectionException;
use Core\Co;

/**
 * Class Context - request context manager
 *
 * @since 2.0
 */
class Context
{
    protected static $contextBean;
    /**
     * Context
     *
     * @var ContextInterface[]
     *
     * @example
     * [
     *    'tid' => ContextInterface,
     *    'tid2' => ContextInterface,
     *    'tid3' => ContextInterface,
     * ]
     */
    private static $context = [];
    /**
     * @var array
     * @example
     * [
     *      'key' => $value,
     *      'key2' => $value2
     * ]
     */
    private static $contextArg = [];

    /**
     * Get context
     * @return ContextInterface|HttpContext
     */
    public static function get(): ?ContextInterface
    {
        $tid = Co::tid();
        return self::$context[$tid] ?? null;
    }

    /**
     * Get context by coID, if not found will throw exception.
     * @return ContextInterface|HttpContext
     */
    public static function current(): ContextInterface
    {
        $tid = Co::tid();

        if (isset(self::$context[$tid])) {
            return self::$context[$tid];
        }

        throw new \Exception('context information has been lost of the coID: ' . $tid);
    }

    /**
     * Set context
     *
     * @param ContextInterface $context
     */
    public static function set(ContextInterface $context): void
    {
        $tid = Co::tid();

        self::$context[$tid] = $context;
    }

    /**
     * @param string $key
     * @param $value
     */
    public static function withValue(string $key,$value):void
    {
        $tid = Co::tid();
        self::$contextArg[$tid][$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function value(string $key)
    {
        $tid = Co::tid();
        if(isset(self::$contextArg[$tid]) && isset(self::$contextArg[$tid][$key]))
            return self::$contextArg[$tid][$key];
        return false;

    }

    /**
     * Get context wait group
     *
     * @return ContextWaitGroup
     *
     * @throws ReflectionException
     */
    public static function getWaitGroup(): ContextWaitGroup
    {
        if (!self::$contextBean){
            self::$contextBean = new ContextWaitGroup();
        }
        return self::$contextBean;
    }

    /**
     * Destroy context
     */
    public static function destroy(): void
    {
        $tid = Co::tid();

        if (isset(self::$context[$tid])) {
            // clear self data.
            self::$context[$tid]->clear();
            unset(self::$context[$tid]);
            unset(self::$contextArg[$tid]);
        }
    }
    /**
     * destory context
     */
    public static function compelete(): void
    {
        sgo(function () {
            // Wait
            Context::getWaitGroup()->wait();

            self::destroy();
        }, false);
    }
}
