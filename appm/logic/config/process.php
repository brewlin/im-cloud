<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/23
 * Time: 13:36
 */
use Process\ProcessManager;
use App\Process\DiscoveryProcess;
use App\Process\InitLogicProcess;

ProcessManager::register("im-logic-discovery",new DiscoveryProcess());


