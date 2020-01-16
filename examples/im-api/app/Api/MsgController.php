<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15 0000
 * Time: 21:56
 */

namespace App\Api;
use App\Models\MsgModel;
use Core\Container\Mapping\Bean;


/**
 * Class MsgController
 * @package App\Api
 * @Bean(prefix="api/im/msg")
 */
class MsgController extends BaseController
{
    /**
     * RequestMapping(route="box/info")
     */
    public function getPersonalMsgBox()
    {
        //返回form和to都为自己的信息
        $this->getCurrentUser();
        $res = \bean(MsgModel::class)->getDataByUserId($this->user['id']);
        return $this->success($res,$this->user);
    }

}