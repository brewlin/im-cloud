<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/15
 * Time: 10:28
 */


namespace Core;

use function count;
use ReflectionException;
use Core\Context\Context;
use Stdlib\Helper\PhpHelper;
use Log\Helper\CLog;
use Swoole\Coroutine;
use Throwable;

/**
 * Class Co
 * @since   2.0
 */
class Co
{
    /**
     * Coroutine id mapping
     *
     * @var array
     * @example
     * [
     *    'child id'  => 'top id',
     *    'child id'  => 'top id',
     *    'child id'  => 'top id'
     * ]
     */
    private static $mapping = [];

    /**
     * Get current coroutine id
     *
     * @return int
     * -1   Not in coroutine
     * > -1 In coroutine
     */
    public static function id(): int
    {
        return Coroutine::getCid();
    }

    /**
     * Get the top coroutine ID
     *
     * @return int
     */
    public static function tid(): int
    {
        $id = self::id();
        return self::$mapping[$id] ?? $id;
    }

    /**
     * Create coroutine
     *
     * @param callable $callable
     * @param bool     $wait
     *
     * @return int If success, return coID
     */
    public static function create(callable $callable, bool $wait = false): int
    {
        $tid = self::tid();

        // return coroutine ID for created.
        return Coroutine::create(function () use ($callable, $tid, $wait) {
            try {
                $id = Coroutine::getCid();
                // Storage fd
                self::$mapping[$id] = $tid;

                if ($wait) {
                    Context::getWaitGroup()->add();
                }

                PhpHelper::call($callable);
            } catch (Throwable $e) {
            CLog::debug(
                    "Coroutine internal error: %s\nAt File %s line %d\nTrace:\n%s",
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine(),
                    $e->getTraceAsString()
                );
            }

            if ($wait) {

                Context::getWaitGroup()->done();
            }
            unset(self::$mapping[$id]);
        });
    }

    /**
     * Write file
     *
     * @param string   $filename
     * @param string   $data
     * @param int|null $flags
     *
     * @return int
     */
    public static function writeFile(string $filename, string $data, int $flags = null): int
    {
        return Coroutine::writeFile($filename, $data, $flags);
    }

    /**
     * Read file
     *
     * @param string $filename
     *
     * @return string
     */
    public static function readFile(string $filename): string
    {
        return Coroutine::readFile($filename);
    }

    /**
     * Multi request
     *
     * @param array $requests
     * @param float $timeout
     *
     * @return array
     * @throws ReflectionException
     */
    public static function multi(array $requests, float $timeout = 0): array
    {
        $count   = count($requests);
        $channel = new Coroutine\Channel($count);

        foreach ($requests as $key => $callback) {
            Co::create(function () use ($key, $channel, $callback) {
                $data = PhpHelper::call($callback);
                $channel->push([$key, $data]);
            });
        }

        $response = [];
        while ($count > 0) {
            $result = $channel->pop($timeout);
            if ($result === false) {
                CLog::info('Co::multi request fail!');
            } else {
                [$key, $value] = $result;
                $response[$key] = $value;
            }

            $count--;
        }

        return $response;
    }
}