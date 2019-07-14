<?php

declare(strict_types=1);


namespace ImQueue\Amqp;

class Result
{
    /**
     * Acknowledge the message.
     */
    const ACK = 'ack';

    /**
     * Reject the message and requeue it.
     */
    const REQUEUE = 'requeue';

    /**
     * Reject the message and drop it.
     */
    const DROP = 'drop';
}
