<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/16 0016
 * Time: 上午 11:29
 */

namespace Database\Event;

use Core\Container\Mapping\Bean;
use Core\Event\EventEnum;
use Core\Event\EventManager;
use Core\Event\Mapping\Event;
use Hyperf\Database\Events\StatementPrepared;
use Log\Helper\Log;
use Psr\EventDispatcher\EventDispatcherInterface;
use PDO;
use Hyperf\Database\Events\QueryExecuted;
/**
 * Class FetchModeEvent
 * @package App\Event
 * @Bean()
 */
class DBDispatcherEvent implements EventDispatcherInterface
{
    /**
     * @param $event
     */
    public function dispatch($event){
        if ($event instanceof QueryExecuted){
            EventManager::trigger(EventEnum::DbQueryExec,$event);
        }
        if ($event instanceof StatementPrepared) {
            EventManager::trigger(EventEnum::DbFetchMode,$event);
        }
    }
}