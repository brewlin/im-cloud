<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2019/1/18
 * Time: 21:54
 */

namespace App\Services;

use App\Models\GroupMemberModel;
use App\Models\MsgModel;
use App\Models\UserGroupMemberModel;
use App\Services\UserCacheService;
use App\Services\UserGroupMemberService;
use App\Task\Task;
use Core\Container\Mapping\Bean;
use Database\Db;

/**
 * Class FriendService
 * @package App\Models\Service
 * @Bean()
 */
class FriendService
{
    /**
     * @param $arr
     * @return array
     */
    public function getFriends($arr)
    {
        $res = [];
        foreach ($arr as $val)
            $res[] = $this->friendInfo(['id'=>$val]);
        return $res;
    }

    /**
     * @param $where
     * @return mixed
     */
    public function friendInfo($where)
    {
        $user = Db::table('user')->where($where)->first();
        $data['id'] = $user['id'];
        $data['avatar'] = $user['avatar'];
        $data['number'] = $user['number'];
        $data['nickname'] = $user['nickname'];
        $data['sign'] = $user['sign'];
        $data['last_login'] = $user['last_login'];
        $data['online']  = \bean(UserCacheService::class)->getFdByNum($user['number'])?1:0;   // 是否在线
        return $data;
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

        $from_user = $this->friendInfo(['number'=>$from_number]);
        $user = $this->friendInfo(['number'=>$number]);
        //获取好友请求方的分组
        $msg = \bean(MsgModel::class)->getDataById($data['msg_id']);
        $user['groupid'] = $msg['group_user_id'];//好友所在分组

        if($from_user['online'])
        {
//            if($check)
//            {
//                (new Task())->sendMsg(['fd' => \bean(UserCacheService::class)->getFdByNum($from_number),'data'=>$user]);
//            }else{
//                $taskData = (new TaskHelper('sendMsg', UserCacheService::getFdByNum($from_number), 'newFriendFail', $number.'('.$user["nickname"].')'.' 拒绝好友申请'))
//                    ->getTaskData();
//            }
//            $taskClass = new Task($taskData);
//            TaskManager::async($taskClass);
        }
    }

    /*
     * 检查二人是否是好友关系
     * @param $user1_id
     * @param $user2_id
     * @return bool
     */
    public function checkIsFriend($user1_id, $user2_id){
        $ids = bean(UserGroupMemberModel::class)->getAllFriends($user1_id);
        if(in_array($user2_id, $ids)){
            return true;
        }
        return false;
    }

}