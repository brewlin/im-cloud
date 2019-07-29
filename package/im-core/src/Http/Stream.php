<?php declare(strict_types=1);

namespace Core\Http;

use BadMethodCallException;
use Exception;
use Psr\Http\Message\StreamInterface;
use ReflectionException;
use RuntimeException;
use const SEEK_SET;
use function strlen;
use Core\Container\Mapping\Bean;

/**
 * Class Stream
 *
 * @Bean(scope=Bean::PROTOTYPE)
 *
 * @since 2.0
 */
class Stream implements StreamInterface
{

    /**
     * @var int
     */
    protected $size = 0;

    /**
     * @var string
     */
    protected $contents = '';

    /**
     * Create stream replace of constructor.
     *
     * @param string $contents
     *
     * @return Stream
     */
    public static function new(string $contents): self
    {
        /** @var Stream $instance */
        $instance = \bean(static::class);

        $instance->contents = $contents;
        $instance->size     = strlen($instance->contents);

        return $instance;
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->contents;
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close(): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @throws BadMethodCallException
     */
    public function detach(): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize(): int
    {
        if (!$this->size) {
            $this->size = strlen($this->getContents());
        }

        return $this->size;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @throws BadMethodCallException on error.
     */
    public function tell()
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @throws BadMethodCallException
     */
    public function eof(): bool
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @throws BadMethodCallException
     */
    public function isSeekable(): bool
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     *
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *                    based on the seek offset. Valid values are identical to the built-in
     *                    PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *                    offset bytes SEEK_CUR: Set position to current location plus offset
     *                    SEEK_END: Set position to end-of-stream plus offset.
     *
     * @throws BadMethodCallException on failure.
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see  seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws BadMethodCallException on failure.
     */
    public function rewind(): void
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        return false;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     *
     * @return int
     * @throws BadMethodCallException on failure.
     */
    public function write($string): int
    {
        throw new BadMethodCallException('Not implemented');

    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return true;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *                    them. Fewer than $length bytes may be returned if underlying stream
     *                    call returns fewer bytes.
     *
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws BadMethodCallException if an error occurs.
     */
    public function read($length): string
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws RuntimeException if unable to read or an error occurs while reading.
     */
    public function getContents(): string
    {
        return $this->contents;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     *
     * @param string $key Specific metadata to retrieve.
     *
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        throw new BadMethodCallException('Not implemented');
    }
}
