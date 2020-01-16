<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/29 0029
 * Time: 上午 10:38
 */

namespace App\Services;

use App\Models\GroupMemberModel;
use App\Models\MsgModel;
use App\Models\UserGroupMemberModel;
use App\Models\UserModel;
use App\Services\UserCacheService;
use Core\Container\Mapping\Bean;

/**
 * Class MemberService
 * @package App\Models\Service
 * @Bean()
 */
class MemberService
{

    /**
     * @param $arr
     * @return mixed
     */
    public function getFriends($arr)
    {
        foreach ($arr as &$group)
        {
            $group['groupname'] = $group['group_name'];
            unset($group['group_name']);
            $group['online'] = 0;
            foreach ($group['list'] as $k => &$friend)
            {
                //检查是否有昵称存在 有则替换当前的昵称
                if(!empty($friend['remark_name']))
                {
                    $name = $friend['remark_name'];
                    $group['list'][$k] = $this->friendInfo(['id' => $friend['friend_id']]);
                    $group['list'][$k]['username'] = $name;
                }else
                {
                    $group['list'][$k] = $this->friendInfo(['id' => $friend['friend_id']]);
                }
            }
        }
        return $arr;
    }

    /**
     * @param $data
     * @param $currentUid
     * 添加好友
     */
    public function newFriends($data ,$currentUid)
    {
        //添加自己的好友
        bean(UserGroupMemberModel::class)->newFriend($currentUid ,$data['friend_id'] ,$data['group_user_id']);
        //请求方添加好友
        //获取消息里的数据
        $friend = \bean(MsgModel::class)->getDataById($data['msg_id']);
        bean(UserGroupMemberModel::class)->newFriend($friend['from'] , $friend['to'] ,$friend['userGroupId']);
    }

    /**
     * @param $where
     * @return UserModel|\Hyperf\Database\Model\Model|object|null
     */
    public function friendInfo($where)
    {
        $user = \bean(UserModel::class)->getUserById($where);
        $status  = \bean(UserCacheService::class)->getTokenByNum($user['number']);
        $user['status'] = $status?'online':'offline';   // 是否在线
        $user['online'] = $status ? true : false;
        return $user;
    }

    /**
     * 处理接收或拒绝添加好友的通知操作
     * @param $data
     */
    public function doReq($data)
    {
        $from_number = $data['from_number'];
        $number      = $data['number'];
        $check       = $data['check'];

        $from_user = \bean(FriendService::class)->friendInfo(['number'=>$from_number]);
        $user = \bean(FriendService::class)->friendInfo(['number'=>$number]);


        if($from_user['online']){
//            if($check){
//                $taskData = (new TaskHelper('sendMsg', UserCacheService::getFdByNum($from_number), 'newFriend', $user))
//                    ->getTaskData();
//            }else{
//                $taskData = (new TaskHelper('sendMsg', UserCacheService::getFdByNum($from_number), 'newFriendFail', $number.'('.$user["nickname"].')'.' 拒绝好友申请'))
//                    ->getTaskData();
//            }
//            $taskClass = new Task($taskData);
//            TaskManager::async($taskClass);
        }

        if($check){
//            if($user['online']){
//                $taskData = (new TaskHelper('sendMsg', UserCacheService::getFdByNum($number), 'newFriend', $from_user))
//                    ->getTaskData();
//                $taskClass = new Task($taskData);
//                TaskManager::async($taskClass);
//            }
        }
    }

    /*
     * 检查二人是否是好友关系
     * @param $user1_id
     * @param $user2_id
     * @return bool
     */
    public  function checkIsFriend($user1_id, $user2_id)
    {
        $ids = bean(UserGroupMemberModel::class)->getAllFriends($user1_id);
        if(in_array($user2_id, $ids)){
            return true;
        }
        return false;
    }

}