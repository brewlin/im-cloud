<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/15 0018
 * Time: 下午 12:35
 */

namespace App\Models;

use Core\Container\Mapping\Bean;
use Database\Db;

/**
 * Class GroupRecordModelDao
 * @package App\Models\Dao
 * @Bean()
 */
class GroupRecordModel
{
    /**
     * @param $data
     */
    public function newRecord($data)
    {
        Db::table('group_record')->insert($data);
    }
    /**
     * @param $current 当前用户的id
     * @param $toId    群对象的id
     * @return array
     */
    public function getAllChatRecordById($uid , $id)
    {
        $recordList = Db::table('group_record')->where('uid','=',$uid)
                            ->where('gnumber','=',$id)
                            ->get(["uid as id","created_time as timestamp","data as content"])
                            ;
        foreach ($recordList as $k => $v)
        {
            $user = Db::table('user')->where('number','=', $v['user_number'])->first();
            $recordList[$k]['username'] = $user['username'];
            $recordList[$k]['avatar'] = $user['avatar'];
        }
        return $recordList;
    }
}