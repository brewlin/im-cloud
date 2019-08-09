<?php

namespace Core\Processor;

use Core\App;

/**
 * Abstract processor
 */
abstract class Processor implements ProcessorInterface
{
    /**
     * core application
     *
     * @var App
     */
    protected $application;

    /**
     * Processor constructor.
     *
     * @param ProcessorInterface application
     */
    public function __construct(App $application)
    {
        $this->application = $application;
    }
}
