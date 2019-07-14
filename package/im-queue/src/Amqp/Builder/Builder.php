<?php

declare(strict_types=1);

namespace ImQueue\Amqp\Builder;

class Builder
{
    protected $passive = false;

    protected $durable = true;

    protected $autoDelete = false;

    protected $nowait = false;

    protected $arguments = [];

    protected $ticket;

    public function isPassive(): bool
    {
        return $this->passive;
    }

    /**
     * @return ExchangeDeclareBuilder
     */
    public function setPassive(bool $passive): self
    {
        $this->passive = $passive;
        return $this;
    }

    public function isDurable(): bool
    {
        return $this->durable;
    }

    /**
     * @return ExchangeDeclareBuilder
     */
    public function setDurable(bool $durable): self
    {
        $this->durable = $durable;
        return $this;
    }

    public function isAutoDelete(): bool
    {
        return $this->autoDelete;
    }

    /**
     * @return ExchangeDeclareBuilder
     */
    public function setAutoDelete(bool $autoDelete): self
    {
        $this->autoDelete = $autoDelete;
        return $this;
    }

    public function isNowait(): bool
    {
        return $this->nowait;
    }

    /**
     * @return ExchangeDeclareBuilder
     */
    public function setNowait(bool $nowait): self
    {
        $this->nowait = $nowait;
        return $this;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return ExchangeDeclareBuilder
     */
    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;
        return $this;
    }

    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param mixed $ticket
     * @return ExchangeDeclareBuilder
     */
    public function setTicket($ticket): self
    {
        $this->ticket = $ticket;
        return $this;
    }
}
