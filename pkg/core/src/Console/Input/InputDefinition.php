<?php declare(strict_types=1);

namespace Core\Console\Input;

/**
 * Class InputDefinition
 */
class InputDefinition
{
    /**
     * @return InputDefinition
     */
    public static function create(): self
    {
        return new self();
    }
}
