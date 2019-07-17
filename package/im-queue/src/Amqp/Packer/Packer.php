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
    public function pack($data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function unpack(string $data)
    {
        return json_decode($data, true);
    }
}
