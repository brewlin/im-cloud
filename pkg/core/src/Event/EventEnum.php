<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/17 0017
 * Time: 上午 10:57
 */

namespace Core\Event;


/**
 * Class EventEnum
 * @package Core\Event
 */
class EventEnum
{
    /** @var string  */
    const AfterFindRouter = "after_find_router_event";

    /** @var string  */
    const DbFetchMode = "database_fetch_mode_event";

    /** @var string  */
    const DbQueryExec = "database_query_exec_event";

    /** @var string */
    const AppException = "app_exception_event";

}