<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/25
 * Time: 22:22
 */

namespace App\Websocket;


use Core\Swoole\HandshakeInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Core\Http\Request\Request as HttpRequest;

class HandshakeListener implements HandshakeInterface
{
    const upgradeUrl = '/sub';
    /**
     * token check '{"mid":123, "room_id":"live://1000", "platform":"web", "accepts":[1000,1001,1002]}'
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public function onHandshake(Request $request, Response $response): bool
    {
        $httpRequest = HttpRequest::new($request);
        //握手失败
        if($httpRequest->getUriPath() != self::upgradeUrl){
            $response->end();
            return false;
        }
        // websocket握手连接算法验证
        $secWebSocketKey = $request->header['sec-websocket-key'];
        $patten = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
        if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
            $response->end();
            return false;
        }
        echo $request->header['sec-websocket-key'];
        $key = base64_encode(sha1(
            $request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
            true
        ));

        $headers = [
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-WebSocket-Accept' => $key,
            'Sec-WebSocket-Version' => '13',
        ];

        // WebSocket connection to 'ws://127.0.0.1:9502/'
        // failed: Error during WebSocket handshake:
        // Response must not include 'Sec-WebSocket-Protocol' header if not present in request: websocket
        if (isset($request->header['sec-websocket-protocol'])) {
            $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }

        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }
        $response->status(101);
        $response->end();
    }

}