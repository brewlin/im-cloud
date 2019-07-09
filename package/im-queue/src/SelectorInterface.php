<?php

namespace ImQueue;

/**
 * the interface of selector
 */
interface SelectorInterface
{
    /**
     * @param string $type
     *
     * @return mixed
     */
    public function select(string $type);
}
