<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/17
 * Time: 17:15
 */

namespace App\Test;


use App\Lib\Producer;

class Consumer
{
    public function con(){
        consumer()->consume(new \App\Lib\Consumer());
    }
    public function pro()
    {
        producer()->produce(new Producer(["test"]));
    }

}