<?php declare(strict_types=1);

namespace Core\Http\Request;

use function array_merge;
use Exception;
use InvalidArgumentException;
use function stripos;
use Core\Http\Stream;
use Core\Http\Upload\UploadedFile;
use Stdlib\Helper\ArrayHelper;
use Stdlib\Helper\JsonHelper;

/**
 * Class InteractsWithInput
 */
trait InteractsWithInput
{
    /**
     * Retrieve a server variable from the request
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function server(string $key = '', $default = null)
    {
        if ($key) {
            return $this->getServerParams()[$key] ?? $default;
        }

        return $this->getServerParams();
    }

    /**
     * Retrieve a header from the request
     *
     * @param string $key
     * @param array  $default
     *
     * @return array
     */
    public function header(string $key = '', array $default = null): array
    {
        if (!$key) {
            return $this->getHeaders();
        }
        return $this->getHeader($key) ?? $default;
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function headerLine(string $key, $default = null)
    {
        if (!$this->hasHeader($key)) {
            return $default;
        }

        return $this->getHeaderLine($key);
    }

    /**
     * Retrieve a query string from the request
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function query(string $key = '', $default = null)
    {
        if (!$key) {
            return $this->getQueryParams();
        }
        return $this->getQueryParams()[$key] ?? $default;
    }

    /**
     * Retrieve a post item from the request
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function post(string $key = '', $default = null)
    {
        if (!$key) {
            return $this->getParsedBody();
        }
        return $this->getParsedBody()[$key] ?? $default;
    }

    /**
     * Get post data
     *
     * @return array
     */
    public function getPost(): array
    {
        return $this->coRequest->post ?? [];
    }

    /**
     * Retrieve a get item from the request
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function get(string $key = '', $default = null)
    {
        if (!$key) {
            return $this->queryParams;
        }
        return $this->queryParams[$key] ?? $default;
    }

    /**
     * Retrieve a input item from the request
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function input(string $key = '', $default = null)
    {
        $parsedBody = $this->getParsedBody();
        $parsedBody = is_array($parsedBody) ? $parsedBody : [];
        $inputs     = array_merge($parsedBody, $this->getQueryParams());
        $inputs     = array_merge($inputs,$this->getPost());

        if (!$key) {
            return $inputs;
        }
        return $inputs[$key] ?? $default;
    }

    /**
     * Retrieve a cookie from the request
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function cookie(string $key = '', $default = null)
    {
        if ($key) {
            return $this->getCookieParams()[$key] ?? $default;
        }

        return $this->getCookieParams();
    }

    /**
     * Retrieve raw body from the request
     *
     * @param null|mixed $default
     *
     * @return array|string|mixed
     */
    public function raw($default = null)
    {
        $body = $this->getBody();
        $raw  = $default;

        if ($body instanceof Stream) {
            $raw = $body->getContents();
        }

        return $raw;
    }

    /**
     * Retrieve a json format raw body from the request,
     * The Content-Type of request must be equal to 'application/json'
     * When Content-Type is not vaild or can not found the key result,
     * The method will always return $default.
     *
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return array|string|mixed
     */
    public function json(string $key = null, $default = null)
    {
        $map = [];
        try {
            $contentType = $this->getHeader('content-type');
            if (!$contentType || false === stripos($contentType[0], 'application/json')) {
                throw new InvalidArgumentException(sprintf('Invalid Content-Type of the request, expects %s, %s given',
                    'application/json', ($contentType ? current($contentType) : 'null')));
            }

            $body = $this->getBody();
            if ($body instanceof Stream) {
                $raw = $body->getContents();
                $map = JsonHelper::decode($raw, true);
            }
        } catch (Exception $e) {
            return $default;
        }

        if ($key === null) {
            return $map ?: $default;
        }

        return ArrayHelper::get($map, $key, $default);
    }

    /**
     * Retrieve a upload item from the request
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return array|UploadedFile|null
     */
    public function file(string $key = '', $default = null)
    {
        if (!$key) {
            return $this->getUploadedFiles();
        }
        return $this->getUploadedFiles()[$key] ?? $default;
    }
}
