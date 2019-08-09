<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/23
 * Time: 18:56
 */

namespace Process\Contract;


use Process\Process as ImProcess;
use Process\ProcessInterface;

abstract class AbstractProcess implements ProcessInterface
{
    /**
     * process name
     * @var string
     */
    public $name = "default process";
    /**
     * if wait process
     * @var bool
     */
    public $boot = false;
    /**
     * redirect_stdin_stdout
     * @var bool
     */
    public $inout = false;
    /**
     * pipe type
     * @var int
     */
    public $pipe = SOCK_STREAM;

    /**
     * crontinue
     * @var bool
     */
    public $co = true;
}