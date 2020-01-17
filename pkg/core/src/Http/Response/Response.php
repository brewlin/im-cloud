<?php declare(strict_types=1);

namespace Core\Http\Response;

use Core\Concern\ContainerTrait;
use Core\Context\Context;
use Core\Http\ContentType;
use Core\Http\Stream;
use function implode;
use function in_array;
use InvalidArgumentException;
use ReflectionException;
use Core\Http\Request\MessageTrait;
use Swoole\Http\Response as CoResponse;
use Throwable;
use Core\Container\Mapping\Bean;
/**
 * Class Response
 * @Bean(name="httpResponse", scope=Bean::PROTOTYPE)
 */
class Response implements ResponseInterface
{
    use MessageTrait;
    const EndCheck = "response_is_end";
    /**
     * @var string
     */
    protected $reasonPhrase = '';

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var string
     */
    protected $charset = 'utf-8';

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Original response data. When this is not null, it will be converted into stream content
     *
     * @var mixed
     */
    protected $data;

    /**
     * Exception
     *
     * @var Throwable|null
     */
    protected $exception;

    /**
     * Coroutine response
     *
     * @var CoResponse
     */
    protected $coResponse;

    /**
     * Default format
     *
     * @var string
     */
    protected $format = self::FORMAT_JSON;

    /**
     * All formatters
     *
     * @var array
     *
     * @example
     * [
     *     Response::FORMAT_JSON => new ResponseFormatterInterface,
     *     Response::FORMAT_XML => new ResponseFormatterInterface,
     * ]
     */
    public $formatters = [];

    /**
     * Cookie
     *
     * @var array
     */
    protected $cookies = [];

    /**
     * @var string
     */
    private $filePath = '';

    /**
     * @var string
     */
    private $fileType = '';
    use ContainerTrait;

    /**
     * Create response replace of constructor
     *
     * @param CoResponse $coResponse
     *
     * @return Response
     */
    public static function new(CoResponse $coResponse): self
    {
//        $self = self::__instance();
        $self = new self();

        // $self = \bean('httpResponse');
        /** @var Response $self */
        $self->coResponse = $coResponse;

        return $self;
    }

    /**
     * Redirect to a URL
     *
     * @param string $url
     * @param int    $status
     *
     * @return static
     */
    public function redirect($url, int $status = 302): self
    {
        $response = $this;
        $response = $response->withAddedHeader('Location', (string)$url)->withStatus($status);

        return $response;
    }

    /**
     * @param string $filePath    like '/path/to/some.jpg'
     * @param string $contentType like 'image/jpeg'
     *
     * @return $this
     */
    public function file(string $filePath, string $contentType): self
    {
        $this->filePath = $filePath;
        $this->fileType = $contentType;
        return $this;
    }

    /**
     * Send response
     */
    public function send(): void
    {
        if(Context::value(self::EndCheck))return;
        // Is send file
        if ($this->filePath) {
            $this->coResponse->header(ContentType::KEY, $this->fileType);
            $this->coResponse->sendfile($this->filePath);
            return;
        }

        // Prepare and send
        $this->quickSend($this->prepare());
    }

    /**
     *
     */
    public function end():Response
    {
        $this->send();
        return $this;
    }

    /**
     * Quick send response
     *
     * @param self|null $response
     */
    public function quickSend(Response $response = null): void
    {
        $response = $response ?: $this;

        // Write Headers to co response
        foreach ($response->getHeaders() as $key => $value) {
            if ($key == ContentType::KEY) {
                $contentType = sprintf(implode(';', $value) . ";charset=%s", $this->getCharset());
                $this->coResponse->header($key, $contentType);
            } else {
                $this->coResponse->header($key, implode(';', $value));
            }
        }

        // TODO ... write cookie

        // Set status code
        $this->coResponse->status($response->getStatusCode());

        // Set body
        $content = $response->getBody()->getContents();
        $this->coResponse->end($content);
        Context::withValue(self::EndCheck,true);
    }

    /**
     * Prepare response
     *
     * @return Response
     */
    private function prepare(): Response
    {
        if(empty($this->format)){
            return $this;
        }

        $formatter = $this->formatters[$this->format] ?? null;

        if ($formatter && $formatter instanceof ResponseFormatterInterface) {
            return $formatter->format($this);
        }

        return $this;
    }

    /**
     * Return new response instance with content
     *
     * @param $content
     *
     * @return static
     */
    public function withContent($content): Response
    {
        if ($this->stream) {
            return $this;
        }

        $new = clone $this;
        if(is_array($content))$content = json_encode($content);
        $new->stream = Stream::new($content);
        return $new;
    }

    /**
     * @return null|Throwable
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    /**
     * @param Throwable $exception
     *
     * @return $this
     */
    public function setException(Throwable $exception): self
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @return CoResponse
     */
    public function getCoResponse(): CoResponse
    {
        return $this->coResponse;
    }

    /**
     * @param CoResponse $coResponse
     *
     * @return $this
     */
    public function setCoResponse(CoResponse $coResponse): self
    {
        $this->coResponse = $coResponse;
        return $this;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * Retrieve attributes derived from the request.
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Retrieve a single derived request attribute.
     * Retrieves a single derived request attribute as described in
     * getAttributes(). If the attribute has not been previously set, returns
     * the default value as provided.
     * This method obviates the need for a hasAttribute() method, as it allows
     * specifying a default value to return if the attribute is not found.
     *
     * @param string $name    The attribute name.
     * @param mixed  $default Default value to return if the attribute does not exist.
     *
     * @return mixed
     * @see getAttributes()
     *
     */
    public function getAttribute($name, $default = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    /**
     * Return an instance with the specified derived request attribute.
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated attribute.
     *
     * @param string $name  The attribute name.
     * @param mixed  $value The value of the attribute.
     *
     * @return static|self
     * @see getAttributes()
     *
     */
    public function withAttribute($name, $value)
    {
        $clone                    = clone $this;
        $clone->attributes[$name] = $value;
        return $clone;
    }

    /**
     * Return instance with the specified data
     *
     * @param mixed $data
     *
     * @return static
     */
    public function withData($data)
    {
        $clone = clone $this;

        $clone->data = $data;
        return $clone;
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @inheritdoc
     *
     * @param int    $code         The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *                             provided status code; if none is provided, implementations MAY
     *                             use the defaults as suggested in the HTTP specification.
     *
     * @return static|self
     * @throws InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $new             = clone $this;
        $new->statusCode = (int)$code;

        if ($reasonPhrase === '' && isset(self::PHRASES[$new->statusCode])) {
            $reasonPhrase = self::PHRASES[$new->statusCode];
        }

        $new->reasonPhrase = $reasonPhrase;
        return $new;
    }

    /**
     * Return an instance with the specified charset content type.
     *
     * @param string $type
     * @param string $charset
     *
     * @return static|self
     */
    public function withContentType(string $type, string $charset = ''): self
    {
        if (!empty($charset)) {
            $this->charset = $charset;
        }

        return $this->withHeader(ContentType::KEY, $type);
    }

    /**
     * @inheritdoc
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     */
    public function setCharset(string $charset): void
    {
        $this->charset = $charset;
    }

    /*******************************************************************************
     * Status check
     ******************************************************************************/

    /**
     * Is this response empty?
     * Note: This method is not part of the PSR-7 standard.
     * @return bool
     */
    public function isEmpty(): bool
    {
        return in_array($this->getStatusCode(), [204, 205, 304], true);
    }

    /**
     * Is this response informational?
     * Note: This method is not part of the PSR-7 standard.
     * @return bool
     */
    public function isInformational(): bool
    {
        return $this->getStatusCode() >= 100 && $this->getStatusCode() < 200;
    }

    /**
     * Is this response OK?
     * Note: This method is not part of the PSR-7 standard.
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->getStatusCode() === 200;
    }

    /**
     * Is this response successful?
     * Note: This method is not part of the PSR-7 standard.
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    /**
     * Is this response a redirect?
     * Note: This method is not part of the PSR-7 standard.
     * @return bool
     */
    public function isRedirect(): bool
    {
        return in_array($this->getStatusCode(), [301, 302, 303, 307], true);
    }

    /**
     * Is this response a redirection?
     * Note: This method is not part of the PSR-7 standard.
     * @return bool
     */
    public function isRedirection(): bool
    {
        return $this->getStatusCode() >= 300 && $this->getStatusCode() < 400;
    }

    /**
     * Is this response forbidden?
     * Note: This method is not part of the PSR-7 standard.
     * @return bool
     * @api
     */
    public function isForbidden(): bool
    {
        return $this->getStatusCode() === 403;
    }

    /**
     * Is this response not Found?
     * Note: This method is not part of the PSR-7 standard.
     * @return bool
     */
    public function isNotFound(): bool
    {
        return $this->getStatusCode() === 404;
    }

    /**
     * Is this response a client error?
     * Note: This method is not part of the PSR-7 standard.
     * @return bool
     */
    public function isClientError(): bool
    {
        return $this->getStatusCode() >= 400 && $this->getStatusCode() < 500;
    }

    /**
     * Is this response a server error?
     * Note: This method is not part of the PSR-7 standard.
     * @return bool
     */
    public function isServerError(): bool
    {
        return $this->getStatusCode() >= 500 && $this->getStatusCode() < 600;
    }
}
