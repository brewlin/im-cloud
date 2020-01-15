<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15 0000
 * Time: 21:50
 */

namespace App\Api;



use App\Models\GroupMemberModel;
use App\Models\Service\MemberService;
use App\Models\UserGroupModel;
use App\Services\UserCacheService;
use Core\Container\Mapping\Bean;

/**
 * Class InitController
 * @package App\Api
 * @Bean("api/im")
 */
class InitController extends BaseController
{
    /**
     * RequestMapping(route="init")
     */
    public function initIm()
    {

        //从缓存服务 获取自己信息
        $token = request()->input('token');
        $user = (new UserCacheService())->getUserByToken($token);
        $user['status'] = 'online';

        // 从用户服务 获取分组好友
        $friends = (new UserGroupModel)->getAllFriends($user['id']);
        $data = (new MemberService)->getFriends($friends);
        //从群组服务 获取群组信息
        $groups = (new GroupMemberModel())->getGroupNames();

        return $this->success(['mine' => $user ,'friend' => $data, 'group' => $groups?$groups:[]],'',0);
    }
}