<?php

declare(strict_types=1);

namespace ImQueue\Amqp\Message;

class Type
{
    const DIRECT = 'direct';

    const FANOUT = 'fanout';

    const TOPIC = 'topic';

    public static function all()
    {
        return [
            self::DIRECT,
            self::FANOUT,
            self::TOPIC,
        ];
    }
}
