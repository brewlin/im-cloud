<?php

declare(strict_types=1);

namespace ImQueue\Amqp\Message;

use ImQueue\Amqp\Builder\QueueBuilder;

interface ConsumerMessageInterface extends MessageInterface
{
    public function consume($data): string;

    public function setQueue(string $queue);

    public function getQueue(): string;

    public function isRequeue(): bool;

    public function getQueueBuilder(): QueueBuilder;
}
