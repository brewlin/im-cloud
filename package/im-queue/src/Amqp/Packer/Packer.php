<?php

declare(strict_types=1);

namespace ImQueue\Amqp\Packer;

use Core\Container\Mapping\Bean;
use Core\Contract\PackerInterface;

/**
 * Class Packer
 * @package ImQueue\Amqp\Packer
 * @Bean()
 */
class Packer implements PackerInterface
{
    const Object = "object";
    const Array = "array";
    public function pack($data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function unpack(string $data)
    {
        $packerType = env("QUEUE_PAKER","array");
        $assoc = true;
        if($packerType == self::Object)
            $assoc = false;

        return json_decode($data, $assoc);
    }
}
