<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/14 0014
 * Time: 上午 9:07
 */

namespace Core\Log;


use Log\Helper\CLog;

/**
 * Console logger
 *
 * @since 2.0
 */
class Logger
{
    /**
     * init log  composer
     */
    public static function initLog(){
        $config = [
            'name'    => 'im-cloud',
            'enable'  => true,
            'output'  => true,
            'levels'  => [],
            'logFile' => ''
        ];
        CLog::init($config);
    }
}