<?php declare(strict_types=1);

namespace Core\Http\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseFormatterInterface
 */
interface ResponseFormatterInterface
{
    /**
     * @param Response $response
     *
     * @return Response|ResponseInterface
     */
    public function format(Response $response): Response;
}
