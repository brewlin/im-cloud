<?php

declare(strict_types=1);


namespace ImQueue\Amqp\Builder;

class ExchangeBuilder extends Builder
{
    protected $exchange;

    protected $type;

    protected $internal = false;

    public function getExchange(): string
    {
        return $this->exchange;
    }

    public function setExchange(string $exchange): self
    {
        $this->exchange = $exchange;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function isInternal(): bool
    {
        return $this->internal;
    }

    public function setInternal(bool $internal): self
    {
        $this->internal = $internal;
        return $this;
    }
}
