<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15 0000
 * Time: 21:55
 */

namespace App\Api;



use App\Models\UserGroupMemberModel;
use App\Models\UserModel;
use Core\Container\Mapping\Bean;

/**
 * Class UserGroupMemberController
 * @package App\Api
 * @Bean(prefix="/api/im/user")
 */
class UserGroupMemberController extends BaseController
{

    /**
     * 编辑好友备注名
     * RequestMapping(route="friend/remark",method={RequestMethod::POST})
     * Strings(from=ValidatorFrom::POST,name="friend_id")
     * Strings(from=ValidatorFrom::POST,name="friend_name")
     * @param Request $request
     */
    public function editFriendRemarkName()
    {
        $data = request()->input();
        $this->getCurrentUser();
        $res = \bean(UserGroupMemberModel::class)->editFriendRemarkName($this->user['id'] , $data['friend_id'] , $data['friend_name']);
        if($res)
            return $this->success($data['friend_name']);
        return $this->error('','修改失败');
    }
    /**
     * 移动好友分组
     * RequestMapping(route="friend/move",method={RequestMethod::POST})
     * Strings(from=ValidatorFrom::POST,name="friend_id")
     * Strings(from=ValidatorFrom::POST,name="groupid")
     * @param Request $request
     */
    public function moveFriendToGroup()
    {
        $data = request()->post();
        $this->getCurrentUser();
        $res = \bean(UserGroupMemberModel::class)->moveFriend($this->user['id'] , $data['friend_id'] , $data['groupid']);
        ;
        if($res)
        {
            //返回好友信息
            $user = \bean(UserModel::class)->getUserById($data['friend_id']);
            return $this->success($user);
        }
        return $this->error('','移动失败');
    }
    /**
     * 删除好友
     * RequestMapping(route="friend/remove",method={RequestMethod::POST})
     * Strings(from=ValidatorFrom::POST,name="friend_id")
     * @param Request $request
     */
    public function removeFriend()
    {
        $data = request()->post();
        $this->getCurrentUser();
        $res = \bean(UserGroupMemberModel::class)->removeFriend($this->user['id'] , $data['friend_id']);
        if($res)
            return $this->success('','删除成功');
        return $this->error('','修改失败');
    }
    /**
     * 获取推荐好友
     * RequestMapping(route="friend/recommend",method={RequestMethod::GET})
     */
    public function getRecommendFriend()
    {
        //获取所有好友
        $list = \bean(UserModel::class)->getAllUser();
        //去除已经是本人的好友关系
        return $this->success($list);
    }
}