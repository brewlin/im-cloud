<?php declare(strict_types=1);


namespace Core\Contract;

/**
 * Class ResourceInterface
 *
 */
interface ResourceInterface
{
    /**
     * Load annotation resource
     */
    public function load(): void;
}