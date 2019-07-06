<?php declare(strict_types=1);

namespace Core\Http;

use ReflectionException;
use Core\Context\AbstractContext;
use Core\Http\Request\Request;
use Core\Http\Response\Response;
use Core\Container\Mapping\Bean;

/**
 * Class HttpContext
 *
 * @Bean(scope=Bean::PROTOTYPE)
 *
 * @since 2.0
 */
class HttpContext extends AbstractContext
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;
    public static $instance;
    public static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Create context replace of construct
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return HttpContext
     */
    public static function new(Request $request, Response $response): self
    {
         $instance = self::getInstance();

        $instance->request  = $request;
        $instance->response = $response;

        return $instance;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Clear resource
     */
    public function clear(): void
    {
        // Clear data
        parent::clear();

        // Clear request/response
        $this->request = $this->response = null;
    }
}
