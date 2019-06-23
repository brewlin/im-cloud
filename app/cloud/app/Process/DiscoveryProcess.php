<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/23
 * Time: 18:30
 */

namespace App\Process;


use Core\Processor\ProcessorInterface;
use Process\Contract\AbstractProcess;
use Process\Process;
use Process\ProcessInterface;

class DiscoveryProcess extends AbstractProcess
{
    public function __construct()
    {
        $this->name = "discovery";
    }

    public function check(): bool
    {
        return true;
        // TODO: Implement check() method.
    }
    public function run(Process $process)
    {
        while(true){
            echo "true"."\n";
            sleep(1);
        }
        // TODO: Implement run() method.
    }
}