<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/14 0014
 * Time: 上午 10:15
 */

namespace Core\Processor;


use Dotenv\Dotenv;
use Log\Helper\CLog;

class EnvProcessor extends Processor
{
    /**
     * Handler env process
     *
     * @return bool
     */
    public function handle(): bool
    {

        $envFile = $this->application->getEnvFile();

        if (!file_exists($envFile)) {
            CLog::warning('Env file(%s) is not exist! skip load it', $envFile);
            return true;
        }
        $path    = dirname($envFile);
        $env     = basename($envFile);
        // Load env
        $dotenv = new Dotenv($path, $env);
        $dotenv->load();

        return true;
    }
}