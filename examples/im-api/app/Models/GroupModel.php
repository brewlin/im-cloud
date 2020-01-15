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

    public function getSum($where)
    {
        return Db::table("group")->where($where)->get()->toArray();
    }

    public function getGroup($where, $single = false){
        if($single)
            return Db::table("group")->where($where)->first()->toArray();
        else
            return Db::table("group")->where($where)->get()->toArray();
    }
    public function getGroupOwnById($id,$key = null)
    {
        $res = Db::table("group")->find($id)->toArray();
        $res['user'] = Db::table('user')->find($res['user_id'])->get()->toArray();
        if($key)
            return $res['user'][$key];
        return $res;
    }

    public function newGroup($data)
    {
        $id =  Db::table("group")->insert($data);
        return $id;
    }
    public function getGroupOwner($id)
    {
        $res = Db::table("group")->find($id)->toArray();
        $res['username'] = Db::table("user")->findOne(['number' => $res['user_number']])->getResult();
        return $res;
    }
    public function getNumberById($id)
    {
        $res = Db::table("group")->find($id)->toArray();
        return $res['gnumber'];
    }
    /**
     * 查找群
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
            ->get()->toArray();
    }

}