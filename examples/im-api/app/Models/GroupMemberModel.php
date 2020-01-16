<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15
 * Time: 23:07
 */

namespace App\Models;

use Core\Container\Mapping\Bean;
use Database\Db;

/**
 * Class GroupMemberModelDao
 * @package App\Models\Dao
 * @Bean()
 */
class GroupMemberModel
{
    /**
     * @param $data
     * @return bool
     */
    public function newGroupMember($data)
    {
        return Db::table('group_member')->insert($data);
    }

    /**
     * @param $where
     * @return \Hyperf\Utils\Collection
     */
    public function getGroups($where)
    {
        $list = Db::table('group_member')->get();
        foreach ($list as $k => $v)
        {
           $list[$k]['info'] = Db::table('grou')->where('number','=',$v['group_number'])->first();

        }
        return $list;
    }

    /**
     * @param $where
     * @return \Hyperf\Database\Model\Model|\Hyperf\Database\Query\Builder|object|null
     */
    public function getOneByWhere($where)
    {
        return Db::table('group_member')->where($where)->first();
    }

    /**
     * @return array
     */
    public function getGroupNames()
    {
        $res = [];
        $list = Db::table('group_member')->get();
        foreach ($list as $group)
        {
            $res[] = Db::table('group_member')->where(
                'number','=', $group['group_number']
            )->get();
        }
        return $res;

    }

    /**
     * @param $gnumber
     * @return \Hyperf\Utils\Collection
     */
    public function getGroupMembers($gnumber)
    {
        return Db::table('group_member')
                        ->where('gnumber','=', $gnumber)
                        ->get('user_number')
                        ;
    }

    /**
     * 删除群成员
     * @param $userNumber
     * @param $groupNumber
     */
    public function delMemberById($userNumber , $groupNumber)
    {
        return Db::table('group_member')->where('user_number','=',$userNumber)
                                    ->where('gnumber','=',$groupNumber)
                                    ->delete();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getNumberById($id)
    {
        $res =  Db::table('group_member')->find($id)->get();
        return $res['gnumber'];
    }

    /**
     * @param $number
     * @return \Carbon\CarbonInterface|\Hyperf\Utils\HigherOrderTapProxy|int|mixed|void
     */
    public function getIdByNumber($number)
    {
        $res =  Db::table('group_member')->where('gnumber' ,'=', $number)->first();
        return $res['id'];
    }

}