<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/8 0008
 * Time: 下午 5:55
 */

namespace App\Service\Model;


/**
 * Class Room
 * @package App\Service\Model
 */
class Room
{
    /**
     * @param $type
     * @param $room
     * @return string
     */
    public static function encodeRoomKey($type,$room)
    {
        return sprintf("%s://%s",$type,$room);
    }

    /**
     * @param $key
     * @return array|bool
     */
    public static function decodeRoomKey($key)
    {
        $parse = parse_url($key);
        if(empty($parse)){
            return false;
        }
        return [$parse["scheme"],$parse["host"].":".$parse["port"]];

    }
}