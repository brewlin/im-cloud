<?php declare(strict_types=1);

namespace Core\Http\Request;

use function array_merge;
use Core\Concern\ContainerTrait;
use Core\Http\ContentType;
use Core\Http\Stream;
use function explode;
use InvalidArgumentException;
use function is_array;
use function preg_replace;
use Psr\Http\Message\StreamInterface;
use ReflectionException;
use function rtrim;
use function strtoupper;
use function substr;
use Core\Http\HttpHelper;
use Stdlib\Helper\Str;
use Swoole\Http\Request as CoRequest;
use Core\Container\Mapping\Bean;

/**
 * Class Request - The PSR ServerRequestInterface implement
 * @Bean(name="httpRequest", scope=Bean::PROTOTYPE)
 */
class Request extends PsrRequest
{
    /**
     * http protocol http1
     */
    public const HTTP1 = "HTTP/1.1";
    /**
     * http protocol http2
     */
    public const HTTP2 = "HTTP/2";
    /**
     * @var string protocol
     */
    protected $protocol;
    /**
     * Router attribute
     */
    public const ROUTER_ATTRIBUTE = 'cloudRouterHandler';

    /**
     * Html
     */
    public const CONTENT_HTML = 'text/html';

    /**
     * Json
     */
    public const CONTENT_JSON = 'application/json';

    /**
     * Xml
     */
    public const CONTENT_XML = 'application/xml';

    /**
     * Method key
     */
    private const METHOD_OVERRIDE_KEY = '_method';

    /**
     * @var CoRequest
     */
    protected $coRequest;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $cookieParams = [];

    /**
     * @var null|array|object
     */
    private $parsedBody;

    /**
     * @var array
     */
    private $parsedQuery = [];

    /**
     * @var array
     */
    private $queryParams = [];

    /**
     * @var array
     */
    private $serverParams = [];

    /**
     * @var array
     */
    private $uploadedFiles = [];

    /**
     * @var string
     */
    private $uriPath = '';

    /**
     * @var string
     */
    private $uriQuery = '';

    /**
     * @var float
     */
    private $requestTime = 0;
    /**
     * @see $_SERVER
     * @var array
     */
    public const DEFAULT_SERVER = [
        'server_protocol'      => 'HTTP/1.1',
        'remote_addr'          => '127.0.0.1',
        'request_method'       => 'GET',
        'request_uri'          => '/',
        'request_time'         => 0,
        'request_time_float'   => 0,
        'query_string'         => '',
        'server_addr'          => '127.0.0.1',
        'server_name'          => 'localhost',
        'server_port'          => 80,
        'script_name'          => '',
        'https'                => '',
        'http_host'            => 'localhost',
        'http_accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'http_accept_language' => 'en-US,en;q=0.8',
        'http_accept_charset'  => 'utf-8;q=0.7,*;q=0.3',
        'http_user_agent'      => 'Unknown',
    ];

    /**
     * All parsers
     *
     * @var array
     *
     * @example
     * [
     *     'content-type' => new XxxParser(),
     *     'content-type' => new XxxParser(),
     *     'content-type' => new XxxParser(),
     * ]
     */
    private $parsers = [];
    use ContainerTrait;

    /**
     * Create Psr server request from swoole request
     *
     * @param CoRequest $coRequest
     *
     * @return Request
     */
    public static function new(CoRequest $coRequest): self
    {
        /** @var Request $self */
//        $self = self::__instance();
        $self = new self();

        $serverParams = array_merge(self::DEFAULT_SERVER, $coRequest->server);

        // Set headers
        $self->initializeHeaders($headers = $coRequest->header ?: []);

        $self->method        = $serverParams['request_method'];
        $self->coRequest     = $coRequest;
        $self->queryParams   = $coRequest->get ?: [];
        $self->cookieParams  = $coRequest->cookie ?: [];
        $self->serverParams  = $serverParams;
        $self->requestTarget = $serverParams['request_uri'];
        $self->protocol      = $serverParams['server_protocol'];

        $parts = explode('?', $serverParams['request_uri'], 2);
        // save
        $self->uriPath  = $parts[0];
        $self->uriQuery = $parts[1] ?? $serverParams['query_string'];

        /** @var Uri $uri */
        $self->uri = Uri::new('', [
            'host'        => $headers['host'] ?? '',
            'path'        => $self->uriPath,
            'query'       => $self->uriQuery,
            'https'       => $serverParams['https'],
            'http_host'   => $serverParams['http_host'],
            'server_name' => $serverParams['server_name'],
            'server_addr' => $serverParams['server_addr'],
            'server_port' => $serverParams['server_port'],
        ]);

        // Update host by Uri info
        if (!isset($headers['host'])) {
            $self->updateHostByUri();
        }

        return $self;
    }

    /**
     * Retrieve server parameters.
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superGlobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * @return array
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * Return an instance with the specified server params.
     *
     * @param array $serverParams
     *
     * @return static
     */
    public function withServerParams(array $serverParams)
    {
        $clone = clone $this;

        $clone->serverParams = $serverParams;
        return $clone;
    }

    /**
     * Retrieve cookies.
     * Retrieves cookies sent by the client to the server.
     * The data MUST be compatible with the structure of the $_COOKIE
     * superGlobal.
     *
     * @return array
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * @inheritdoc
     *
     * @return static
     */
    public function withCookieParams(array $cookies)
    {
        $clone = clone $this;

        $clone->cookieParams = $cookies;
        return $clone;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * add param
     *
     * @param string $name  the name of param
     * @param mixed  $value the value of param
     *
     * @return static
     */
    public function addQueryParam(string $name, $value)
    {
        $clone = clone $this;

        $clone->queryParams[$name] = $value;
        return $clone;
    }

    /**
     * @inheritdoc
     *
     * @return static
     */
    public function withQueryParams(array $query)
    {
        $clone = clone $this;

        $clone->queryParams = $query;
        return $clone;
    }

    /**
     * @inheritdoc
     * @return array An array tree of UploadedFileInterface instances; an empty
     *     array MUST be returned if no data is present.
     */
    public function getUploadedFiles(): array
    {
        if ($this->uploadedFiles) {
            return $this->uploadedFiles;
        }

        if ($files = $this->coRequest->files) {
            $this->uploadedFiles = HttpHelper::normalizeFiles($files);
        }

        return $this->uploadedFiles;
    }

    /**
     * @inheritdoc
     *
     * @return static
     * @throws InvalidArgumentException if an invalid structure is provided.
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $clone = clone $this;

        $clone->uploadedFiles = $uploadedFiles;
        return $clone;
    }

    /**
     * Returns the raw HTTP request body.
     * @return string the request body
     */
    public function getRawBody(): string
    {
        $body = $this->coRequest->rawContent();
        return ($body === false) ? '' : $body;
    }

    /**
     * @inheritdoc
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody()
    {
        // Need init
        if ($this->parsedBody !== null) {
            return $this->parsedBody;
        }

        $parsedBody = $this->coRequest->post ?? [];

        $needles     = [
            ContentType::FORM,
            ContentType::FORM_DATA,
        ];
        $contentType = $this->getHeaderLine(ContentType::KEY);
        if (Str::contains($contentType, $needles)) {
            $this->parsedBody = $parsedBody;
            return $parsedBody;
        }

        // Parse body
        if (!$parsedBody && !$this->isGet()) {
            $rawBody = $this->getRawBody();
            if (!empty($rawBody)) {
                $parsedBody = $this->parseRawBody($rawBody);
            }
        }

        $this->parsedBody = $parsedBody;
        return $this->parsedBody;
    }

    /**
     * @return array
     */
    public function getParsedQuery(): array
    {
        return $this->parsedQuery;
    }

    /**
     * @param array $query
     *
     * @return Request
     */
    public function withParsedQuery(array $query)
    {
        $clone = clone $this;

        $clone->parsedQuery = $query;
        return $clone;
    }

    /**
     * @param string $key
     * @param mixed|null   $default
     *
     * @return mixed|null
     */
    public function parsedBody(string $key, $default = null)
    {
        $parseBody = $this->getParsedBody();
        return $parseBody[$key] ?? $default;
    }

    /**
     * Add parser body
     *
     * @param string $name  the name of param
     * @param mixed  $value the value of param
     *
     * @return static
     */
    public function addParsedBody(string $name, $value)
    {
        if (!is_array($this->parsedBody)) {
            return $this;
        }

        $clone = clone $this;

        $clone->parsedBody[$name] = $value;
        return $clone;
    }

    /**
     * @inheritdoc
     * @return static
     * @throws InvalidArgumentException if an unsupported argument type is provided.
     */
    public function withParsedBody($data)
    {
        $clone = clone $this;

        $clone->parsedBody = $data;
        return $clone;
    }

    /**
     * @inheritdoc
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritdoc
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
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @inheritdoc
     *
     * @param string $name  The attribute name.
     * @param mixed  $value The value of the attribute.
     *
     * @return static
     * @see getAttributes()
     *
     */
    public function withAttribute($name, $value)
    {
        $clone = clone $this;

        $clone->attributes[$name] = $value;
        return $clone;
    }

    /**
     * @inheritdoc
     *
     * @param string $name The attribute name.
     *
     * @return static
     * @see getAttributes()
     *
     */
    public function withoutAttribute($name)
    {
        if (!isset($this->attributes[$name])) {
            return $this;
        }

        $clone = clone $this;

        unset($clone->attributes[$name]);
        return $clone;
    }

    /**
     * Get the URL (no query string) for the request.
     *
     * @return string
     */
    public function url(): string
    {
        return rtrim(preg_replace('/\?.*/', '', $this->getUri()), '/');
    }

    /**
     * Get the full URL for the request.
     *
     * @return string
     */
    public function fullUrl(): string
    {
        $query    = $this->getUriQuery();
        $question = $this->getUri()->getHost() . ($this->getUriPath() === '/' ? '/?' : '?');
        return $query ? $this->url() . $question . $query : $this->url();
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->coRequest->fd;
    }

    /**
     * @return string
     */
    public function getUriPath(): string
    {
        return $this->uriPath;
    }

    /**
     * @return string
     */
    public function getUriQuery(): string
    {
        return $this->uriQuery;
    }

    /**
     * Determine if the request is the result of an ajax call.
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->isXmlHttpRequest();
    }

    /**
     * @inheritdoc
     * @see http://en.wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
     *
     * @return bool true if the request is an XMLHttpRequest, false otherwise
     */
    public function isXmlHttpRequest(): bool
    {
        return 'XMLHttpRequest' === $this->getHeaderLine('X-Requested-With');
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        if ($method = $this->post(self::METHOD_OVERRIDE_KEY)) {
            return strtoupper($method);
        }

        return parent::getMethod();
    }

    /**
     * @return StreamInterface
     * @throws ReflectionException
     */
    public function getBody(): StreamInterface
    {
        if (!$this->stream) {
            $this->stream = Stream::new($this->coRequest->rawContent());
        }

        return $this->stream;
    }

    /**
     * @return CoRequest
     */
    public function getCoRequest(): CoRequest
    {
        return $this->coRequest;
    }

    /**
     * @param CoRequest $coRequest
     */
    public function setCoRequest(CoRequest $coRequest): void
    {
        $this->coRequest = $coRequest;
    }

    /**
     * @return float
     */
    public function getRequestTime(): float
    {
        return (float)$this->serverParams['request_time_float'];
    }

    /**
     * Get protocol version
     * @return string
     */
    public function getProtocolVersion(): string
    {
        if (!$this->protocol) {
            // $self->protocol = \str_replace('HTTP/', '', $serverParams['server_protocol']);
            $this->protocol = substr($this->serverParams['server_protocol'], 5); // faster
        }

        return $this->protocol;
    }

    /**
     * @param string $content
     *
     * @return mixed
     */
    private function parseRawBody(string $content)
    {
        $contentTypes = $this->getHeader(ContentType::KEY);
        foreach ($contentTypes as $contentType) {
            $parser = $this->parsers[$contentType] ?? null;
            if ($parser && $parser instanceof RequestParserInterface) {
                return $parser->parse($content);
            }
        }

        return $content;
    }

    /**
     * get http protocol
     * @return string
     */
    public function getProtocol():string
    {
        return $this->protocol;
    }
}
