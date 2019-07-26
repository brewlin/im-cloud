<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/23
 * Time: 13:36
 */
use Process\ProcessManager;
use App\Process\DiscoveryProcess;

ProcessManager::register("im-cloud-discovery",new DiscoveryProcess());