<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/14 0014
 * Time: 上午 11:18
 */

namespace Core\Processor;


use Stdlib\Helper\ArrayHelper;

class AppProcessor extends Processor
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors = [];

    /**
     * Handle application processors
     */
    public function handle(): bool
    {

        foreach ($this->processors as $processor) {
            $class = get_class($processor);

            // If is disabled, skip handle.

            $processor->handle();
        }

        return true;
    }

    /**
     * Add first processor
     *
     * @param Processor[] $processor
     * @return bool
     */
    public function addFirstProcessor(Processor ...$processor): bool
    {
        array_unshift($this->processors, ... $processor);

        return true;
    }

    /**
     * Add last processor
     *
     * @param Processor[] $processor
     *
     * @return bool
     */
    public function addLastProcessor(Processor ...$processor): bool
    {
        array_push($this->processors, ... $processor);

        return true;
    }

    /**
     * Add processors
     *
     * @param int         $index
     * @param Processor[] $processors
     *
     * @return bool
     */
    public function addProcessor(int $index, Processor  ...$processors): bool
    {
        ArrayHelper::insert($this->processors, $index, ...$processors);

        return true;
    }
}