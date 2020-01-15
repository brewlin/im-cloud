<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/28 0028
 * Time: 下午 5:46
 */

namespace App\Api;


use App\Task\LogicPush;
use Core\Container\Mapping\Bean;
use Core\Context\Context;
use Database\Db;
use Im\Cloud\Operation;
use Task\Task;

/**
 * Class PushRoomController
 * @package App\Api
 * @Bean()
 */
class IndexController extends BaseController
{
    /**
     * @return PushRoomController|\Core\Http\Response\Response
     * @throws \Exception
     */
    public function index(){
        $res = Db::table("test")->get();
        var_dump($res);
    }

}