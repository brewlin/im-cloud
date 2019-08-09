<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/18
 * Time: 11:04
 */
namespace Process;

use Process\Process;

/**
 * The process interface
 */
interface ProcessInterface
{
    /**
     * @param Process $process
     */
    public function run(Process $process);

    /**
     * @return bool
     */
    public function check(): bool;
}
