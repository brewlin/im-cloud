<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15 0000
 * Time: 21:50
 */

namespace App\Api;



use App\Models\GroupMemberModel;
use App\Models\UserGroupModel;
use App\Services\MemberService;
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
    public function init()
    {

        //从缓存服务 获取自己信息
        $token = request()->input('token');
        $user = \bean(UserCacheService::class)->getUserByToken($token);
        $this->user['status'];
        $user['status'] = 'online';

        // 从用户服务 获取分组好友
        $friends = \bean(UserGroupModel::class)->getAllFriends($user['id']);
        $data = \bean(MemberService::class)->getFriends($friends);
        //从群组服务 获取群组信息
        $groups = \bean(GroupMemberModel::class)->getGroupNames();

        return $this->success(['mine' => $user ,'friend' => $data, 'group' => $groups?$groups:[]],'',0);
    }
}