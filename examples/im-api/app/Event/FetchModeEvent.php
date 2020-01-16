<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/16 0016
 * Time: 上午 11:29
 */

namespace App\Event;

use Core\Container\Mapping\Bean;
use Hyperf\Database\Events\StatementPrepared;
use Psr\EventDispatcher\EventDispatcherInterface;
use PDO;

/**
 * Class FetchModeEvent
 * @package App\Event
 * @Bean()
 */
class FetchModeEvent implements EventDispatcherInterface
{
    /**
     * @param $event
     */
    public function dispatch($event){
        if ($event instanceof StatementPrepared) {
            $this->process($event);
        }

    }

    /**
     * @param object $event
     */
    public function process(object $event)
    {
        if ($event instanceof StatementPrepared) {
            $event->statement->setFetchMode(PDO::FETCH_ASSOC);
        }
    }
}