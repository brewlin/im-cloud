<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/6/25 0025
 * Time: 下午 12:22
 */

namespace App\Api;



use Core\Context\Context;

class HealthController
{
    public function health(){
        return Context::get()
            ->getResponse()
            ->withStatus(200)
            ->withContent("ok");
    }

}