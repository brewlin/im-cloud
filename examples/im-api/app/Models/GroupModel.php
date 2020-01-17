<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/15 0016
 * Time: 上午 9:31
 */

namespace App\Models;


use Core\Container\Mapping\Bean;
use Database\Db;

/**
 * Class GroupModelDao
 * @package App\Models\Dao
 * @Bean()
 */
class GroupModel
{

    /**
     * @param $where
     * @return \Hyperf\Utils\Collection
     */
    public function getSum($where)
    {
        return Db::table("group")->where($where)->get();
    }

    /**
     * @param $where
     * @param bool $single
     * @return \Hyperf\Database\Model\Model|\Hyperf\Database\Query\Builder|\Hyperf\Utils\Collection|object|null
     */
    public function getGroup($where, $single = false){
        if($single)
            return Db::table("group")->where($where)->first();
        else
            return Db::table("group")->where($where)->get();
    }

    /**
     * @param $id
     * @param null $key
     * @return \Hyperf\Database\Query\Builder|mixed
     */
    public function getGroupOwnById($id,$key = null)
    {
        $res = Db::table("group")->find($id);
        $res['user'] = Db::table('user')->find($res['user_id'])->get();
        if($key)
            return $res['user'][$key];
        return $res;
    }

    /**
     * @param $data
     * @return bool
     */
    public function newGroup($data)
    {
        $id =  Db::table("group")->insert($data);
        return $id;
    }

    /**
     * @param $id
     * @return \Hyperf\Database\Query\Builder|mixed
     */
    public function getGroupOwner($id)
    {
        $res = Db::table("group")->find($id);
        $res['username'] = Db::table("user")->where(['number' => $res['user_number']])->first();
        return $res;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getNumberById($id)
    {
        $res = Db::table("group")->find($id);
        return $res['gnumber'];
    }
    /**
     * 查找群
     * @param $value
     * @param null $page
     * @return \Hyperf\Utils\Collection
     */
    public function searchGroup($value ,$page = null)
    {
        if(!$value)
            return Db::table("group")->findAll()->getResult();
        return Db::table("group")
            ->orWhere('ginfo','like','%'.$value.'%')
            ->orWhere('gname','like','%'.$value.'%')
            ->orWhere('gnumber','like','%'.$value.'%')
            ->orWhere('number','like','%'.$value.'%')
            ->get();
    }

}