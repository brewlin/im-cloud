<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/11/29 0029
 * Time: 下午 3:24
 */

namespace App\Service\Service;

use App\Packet\Packet;
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use Log\Helper\Log;

/**
 * Class Push
 * @package App\Service\Service
 * @Bean()
 */
class Push
{
    const Action = "cmd";
    const Body = "data";

    const Exector = ["pushKeys","pushMids","pushRoom","pushAll"];
    /**
     * 推送数据到logic节点处理
     */
    public function push()
    {
        /** @var array  $data */
        $packet = Context::value(Packet::class);
        //数据包格式错误
        if(!isset($packet[self::Action]) || !in_array($packet[self::Action],self::Exector) || !is_array($packet) || !isset($packet[self::Body])){
            Log::error(sprintf("parse client data error,client fd:%s,data:%s",Context::value(),is_array($packet)?json_encode($packet):$packet));
        }
        $this->{$packet[self::Action]}($packet[self::Body]);
    }
    public function pushKeys($data)
    {

    }
    public function pushMids($data)
    {

    }
    public  function pushRoom($data)
    {

    }
    public function pushAll($data)
    {

    }

}