<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/18
 * Time: 11:04
 */
namespace Process;

use Process\Process as ImProcess;

/**
 * The process interface
 */
interface ProcessInterface
{
    /**
     * @param ImProcess $process
     */
    public function run(ImProcess $process);

    /**
     * @return bool
     */
    public function check(): bool;
}
