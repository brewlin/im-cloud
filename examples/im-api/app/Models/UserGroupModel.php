<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/15 0015
 * Time: 下午 12:45
 */

namespace App\Models;

use Core\Container\Mapping\Bean;
use Database\Db;

/**
 * Class UserGroupModelDao
 * @package App\Models\Dao
 * @Bean()
 */
class UserGroupModel
{
    /**
     * @param $id
     * @return \Hyperf\Utils\Collection
     */
    public function getAllFriends($id)
    {
        $list = Db::table('user_group')->where('user_id','=',$id)->get()->toArray();
        foreach ($list as $k => $v)//
        {
           $list[$k]['list'] = Db::table('user_group_member')->where('user_group_id','=',$v['id'])->get()->toArray();
        }
        return $list;
    }

    /**
     * 添加分组
     * @param $userId 用户id
     * @param $groupname 分组名
     */
    public function addGroup($userId , $groupname)
    {
        $data['user_id'] = $userId;
        $data['group_name'] = $groupname;
        $data['status'] = 1;
        return Db::table('user_gorup')->insert($data);
    }

    /**
     * 修改分组名
     * @param $userId
     * @param $groupname
     */
    public function editGroup($id , $groupname)
    {
        return Db::table('user_group')->where('id','=',$id)
                                            ->update(['group_name' => $groupname]);
    }

    /**
     * @param $attr
     * @param $condition
     * @param bool $single
     * @return int
     */
    public function updateByWhere($attr , $condition ,$single = true)
    {
        if($single)
            return Db::table('user_group')->where($condition)->update($attr);
        return Db::table('user_group')->where($condition)->update($attr);
    }
    /**
     * 删除分组名
     * 检查下面是否有好友
     * 将好友转移到默认分组去
     */
    public function delGroup($id ,  $user)
    {
        $group = Db::table('user_group')->find($id)->first();
        if($group['user_id'] != $user['id'])
        {
            return false;
        }
        $default = $this->getDefaultGroupUser($user['id']);
        Db::table('user_group_member')->where(['user_id' => $user['id'],'user_group_id' => $id])
                                             ->update(['user_group_id' => $default['id']]);
        return Db::table('user_group')->delete($id);
    }
    /**
     * 获取用户第一个分组信息
     */
    public function getDefaultGroupUser($userId)
    {
        return Db::table('user_group')
                        ->where('user_id','=',$userId)
                        ->orderBy('id')
                        ->first();
    }
}