<?php

declare(strict_types=1);


namespace ImQueue\Amqp\Message;

use ImQueue\Amqp\Constants;
use ImQueue\Amqp\Packer\Packer;
use ImQueue\Utils\ApplicationContext;

abstract class ProducerMessage extends Message implements ProducerMessageInterface
{
    /**
     * @var string
     */
    protected $payload = '';

    /**
     * @var array
     */
    protected $properties
        = [
            'content_type' => 'text/plain',
            'delivery_mode' => Constants::DELIVERY_MODE_PERSISTENT,
        ];

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setPayload($data): self
    {
        $this->payload = $data;
        return $this;
    }

    public function payload(): string
    {
        return $this->serialize();
    }

    public function serialize(): string
    {
        $packer = bean(Packer::class);
        return $packer->pack($this->payload);
    }
}
