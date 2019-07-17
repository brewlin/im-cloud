<?php

declare(strict_types=1);

namespace ImQueue\Amqp\Message;

use ImQueue\Amqp\Builder\QueueBuilder;
use ImQueue\Amqp\Packer\Packer;
use Psr\Container\ContainerInterface;

abstract class ConsumerMessage extends Message implements ConsumerMessageInterface
{
    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * @var string
     */
    protected $queue;

    /**
     * @var bool
     */
    protected $requeue = true;

    public function setQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function getQueue(): string
    {
        return $this->queue;
    }

    public function isRequeue(): bool
    {
        return $this->requeue;
    }

    public function getQueueBuilder(): QueueBuilder
    {
        return (new QueueBuilder())->setQueue($this->getQueue());
    }

    public function unserialize(string $data)
    {
        $packer = bean(Packer::class);
        return $packer->unpack($data);
    }
}
