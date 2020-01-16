<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2020/1/16 0016
 * Time: 上午 10:05
 */

namespace Core\Http\Response;

use Core\Container\Mapping\Bean;

/**
 * Class ErrMsg
 * @package Core\Http\Response
 * @Bean()
 */
class ErrMsg
{
    const METHOD_NOT_ALLOWED = "not support this method";
    const NOT_FOUND = "not found that action";
    const SERVER_ERROR =" internel error";

    /**
     * @param $msg
     * @return false|string
     */
    public static function err($msg)
    {
        $formt = ["code" => 0,"msg" => $msg];
        return json_encode($formt);
    }

}