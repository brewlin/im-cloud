<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/9 0009
 * Time: 下午 2:22
 */

namespace App\Event;

use App\Connection\Bucket;
use App\Connection\Connection;
use Core\Container\Mapping\Bean;

/**
 * Class OnCloseListener
 * @package App\cloud\app\Event
 * @Bean()
 */
class Close
{
    /**
     * onClose event
     *
     * @param integer $fd
     * @return void
     */
    public function close(Connection $conn): void
    {
        /**
         * 表示为tcp连接
         * 表示为websocket连接
         */
        if($conn->isWs() || $conn->isTcp()){
            /** Task */
            bean(Bucket::class)->disconnect($conn->getFd());
            return;
        }
    }

}