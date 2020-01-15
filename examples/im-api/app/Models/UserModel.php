<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/15 0015
 * Time: 下午 12:40
 */

namespace App\Models;

use Core\Container\Mapping\Bean;
use Database\Db;

/**
 * Class UserDao
 * @package App\Models\Dao
 * @Bean()
 */
class UserModel
{
    public function getUser($where,$single = false)
    {
        if($single)
            return Db::table('user')->where($where)->first();
        return Db::table('user')->where($where)->get()->toArray();
    }
    public function newUser($data)
    {
        return Db::table('user')->insert($data);
    }
    public function updateByWhere($attr , $condition,$single = true)
    {
        if($single)
            return Db::table('user')->where($condition)->update($attr);
        return Db::table('user')->where($condition)->update($attr);

    }
    public function updateUser($id,$data)
    {
        return Db::table('user')->where('id','=',$id)
                                       ->update($data);
    }
    public function getUserByNumbers($numbers)
    {
        $data = [];
        foreach ($numbers as $k => $v)
        {
            $data[] = Db::table('user')->where('number' ,'=', $v)->first();
        }
        return $data;
    }
    public function getNumberById($id)
    {
        $user = Db::table('user')->find($id)->first();
        return $user['number'];
    }
    public function getUserById($id)
    {
        return Db::table('user')->find($id)->first();
    }
    public function getAllUser()
    {
        return Db::table('user')->get()->toArray();
    }
    public function searchUser($value , $page = null)
    {
        if(!$value)
            return Db::table('user')->get()->toArray();
        return Db::table('user')->query()
            ->orWhere('number','like','%'.$value.'%')
            ->orWhere('nickname','like','%'.$value.'%')
            ->orWhere('phone','like','%'.$value.'%')
            ->orWhere('email','like','%'.$value.'%')
            ->get()->toArray();
    }
}