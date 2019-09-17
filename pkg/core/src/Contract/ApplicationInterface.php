<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/17 0017
 * Time: 下午 5:16
 */

namespace Core\Contract;

/**
 * Interface ApplicationInterface
 * @package Core\Contract
 */
interface ApplicationInterface
{
    /**
     * @return void
     */
    public function handle():void;

}