<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15
 * Time: 1:12
 */

namespace App\Models;

use Core\Container\Mapping\Bean;
use Database\Db;

/**
 * Class UserRecordModelDao
 * @package App\Models\Dao
 * @Bean()
 */
class UserRecordModel
{
    /**
     * @param $where
     * @param $data
     * @return int
     */
    public function updateByWhere($where ,$data)
    {
        return Db::table('user_record')->where($where)->update($data);
    }

    /**
     * @param $data
     * @return bool
     */
    public function newRecord($data)
    {
        return Db::table('user_record')->insert($data);
    }

    /**
     * @param $value
     * @return float|int
     */
    public function getTimeStampAttr($value)
    {
        return strtotime($value)*1000;
    }
    /**
     * @param $current 当前用户的id
     * @param $toId    聊天对象的id
     * @return array
     */
    public function getAllChatRecordById($current , $toId)
    {
        $recordList1 = Db::table('user_record')->where('user_id' ,'=',$current)->where('friend_id','=',$toId)->get();
        $recordList2 = Db::table('user_record')->where('user_id' ,'=',$toId)->where('friend_id','=',$current)->get();
        $recordList = array_merge($recordList1,$recordList2);
        foreach ($recordList as $k => $v)
        {
            unset($recordList1[$k]['id']);
            $recordList1[$k]['id'] = $v['userId'];
            $recordList1[$k]['timestamp'] = $v['createTime'];
            $recordList1[$k]['content'] = $v['data'];
            $user = Db::table('user')->where('id','=',$v['user_id'])->first();
            $recordList[$k]['username'] = $user['username'];
            $recordList[$k]['avatar'] = $user['avatar'];
        }
        return $recordList;
    }
    /**
     * 查看未读聊天记录
     */
    public function  getAllNoReadRecord($uid)
    {
        $list = Db::table('user_record')
            ->where('user_id','=',$uid)
            ->where('is_read','=',0)
            ->get();
        foreach ($list as $k => $v)
        {
            $user = Db::table('user')->find($v['userId'])->first();
            $touser = Db::table('user')->find($v['friendId'])->first();
            $list[$k]['user'] = $user;
            $list[$k]['touser'] = $touser;
        }
        return $list;
    }
}