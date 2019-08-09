<?php

declare(strict_types=1);

namespace ImQueue\Amqp\Message;

interface ProducerMessageInterface extends MessageInterface
{
    public function setPayload($data);

    public function payload(): string;

    public function getProperties(): array;
}
