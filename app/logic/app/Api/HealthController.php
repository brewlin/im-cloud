<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/25 0025
 * Time: 下午 12:22
 */

namespace App\Api;



use Core\Context\Context;

/**
 * 心跳的健康检查，在服务发现时配置的check接口
 * Class HealthController
 * @package App\Api
 */
class HealthController
{
    /**
     * 只要回复状态是 200 即可
     * @return \Core\Http\Response\Response|static
     */
    public function health(){
        return Context::get()
            ->getResponse()
            ->withStatus(200)
            ->withContent("ok");
    }

}