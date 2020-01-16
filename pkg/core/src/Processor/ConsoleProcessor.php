<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/14 0014
 * Time: 下午 3:24
 */

namespace Core\Processor;


use Core\App;
use Core\Config\Config;
use Core\Console\Cli;
use Core\Console\Console;

/**
 * Class ConsoleProcessor
 * @package Core\Processor
 */
class ConsoleProcessor extends Processor
{
    /**
     * @return bool
     */
    public function handle(): bool
    {
        $arg = getopt("",["start","restart","stop","reload","d","debug","log:","h","v"]);
        $this->handleArg($arg);
        return true;
    }

    /**
     * @param array $arg
     */
    public function handleArg(array $arg){
        /** @var Cli $cli */
        $cli = new Cli();
        $cli->showApplicationHelp();
        if(empty($arg) || isset($arg['h'])){
            exit;
        }
        if(isset($arg['v'])){
            Console::colored(App::FONT_LOGO, 'cyan');
            Console::writeln(sprintf('<success>version:</success>%s',$cli->getVersion()));
            exit;
        }
        if(isset($arg['d'])){
            putenv("DAEMONIZE=1");
        }
        if(isset($arg["log"])){
            putenv("START_LOG={$arg["log"]}");
        }
        if(isset($arg["debug"])){
            putenv("APP_DEBUG=1");
        }
        if(isset($arg["start"])){
            putenv("APP=start");
        }
        if(isset($arg["restart"])){
            putenv("APP=restart");
            putenv("DAEMONIZE=1");
        }
        if(isset($arg["reload"])){
            putenv("APP=reload");
        }
        if(isset($arg["stop"])){
            putenv("APP=stop");
        }

    }


}