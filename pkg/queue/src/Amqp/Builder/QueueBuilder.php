<?php

declare(strict_types=1);


namespace ImQueue\Amqp\Builder;

class QueueBuilder extends Builder
{
    protected $queue;

    protected $exclusive = false;

    protected $arguments = [
        'x-ha-policy' => ['S', 'all'],
    ];

    public function getQueue(): string
    {
        return $this->queue;
    }

    public function setQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function isExclusive(): bool
    {
        return $this->exclusive;
    }

    public function setExclusive(bool $exclusive): self
    {
        $this->exclusive = $exclusive;
        return $this;
    }
}
