<?php

declare(strict_types=1);

namespace ImQueue\Amqp\Message;

use ImQueue\Amqp\Builder\ExchangeBuilder;

interface MessageInterface
{
    /**
     * Pool name for amqp.
     */
    public function getPoolName(): string;

    public function setType(string $type);

    public function getType(): string;

    public function setExchange(string $exchange);

    public function getExchange(): string;

    public function setRoutingKey(string $routingKey);

    public function getRoutingKey(): string;

    public function getExchangeBuilder(): ExchangeBuilder;

    // $passive = false,
    // $durable = false,
    // $auto_delete = true,
    // $internal = false,
    // $nowait = false,
    // $arguments = array(),
    // $ticket = null

    /**
     * Serialize the message body to a string.
     */
    public function serialize(): string;

    /**
     * Unserialize the message body.
     */
    public function unserialize(string $data);
}
