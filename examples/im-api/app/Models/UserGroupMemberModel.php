<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/15 0015
 * Time: 下午 12:30
 */

namespace App\Models;

use Core\Container\Mapping\Bean;
use Database\Db;

/**
 * Class UserGroupMemberDao
 * @package App\Models\Dao
 * @Bean()
 */
class UserGroupMemberModel
{
    public function getAllFriends($id)
    {
        return Db::table('user_group_member')->where('user_id','=', $id)->get()->toArray();
       // return self::where('user_id',$id)->column('friend_id');
    }

    public function newFriend($uId, $friendId , $groupId )
    {
        return Db::table('user_group_member')->insert(['user_id' => $uId,'friend_id' => $friendId,'user_group_id' => $groupId]);
    }
    /**
     * 修改好友备注名
     */
    public function editFriendRemarkName($uid , $friendId , $remark)
    {
        return Db::table('user_group_member')
                        ->where([['user_id' ,'=', $uid],['friend_id' ,'=',$friendId]])
                        ->update(['remar_name' => $remark]);
    }
    /**
     * 移动联系人
     * @param $uid 自己的id
     * @param $friendId 被移动的好友id
     * @param $groupid 移动的目标分组id
     */
    public function moveFriend($uid , $friendId , $groupid)
    {
        return Db::table('user_group_member')->where('user_id','=', $uid)
                                                    ->where('friend_id' ,'=', $friendId)
                                                    ->update(['groupid' => $groupid]);
    }
    public  function removeFriend($uid , $fiendId)
    {
        return Db::table('user_group_member')->where('user_id','=', $uid)
            ->where('friend_id' ,'=', $fiendId)->delete();
    }
}