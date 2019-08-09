<?php declare(strict_types=1);

namespace Core\Swoole;

use Swoole\Http\Request;
use Swoole\Websocket\Server;

/**
 * Interface OpenInterface
 *
 * @since 2.0
 */
interface OpenInterface
{
    /**
     * Open event
     *
     * @param Server  $server
     * @param Request $request
     */
    public function onOpen(Server $server, Request $request): void;
}
