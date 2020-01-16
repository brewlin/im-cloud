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
    /**
     * @param $where
     * @param bool $single
     * @return \Hyperf\Database\Model\Model|\Hyperf\Database\Query\Builder|\Hyperf\Utils\Collection|object|null
     */
    public function getUser($where,$single = true)
    {
        if($single)
            return Db::table('user')->where($where)->first();
        return Db::table('user')->where($where)->get();
    }

    /**
     * @param $data
     * @return bool
     */
    public function newUser($data)
    {
        return Db::table('user')->insert($data);
    }

    /**
     * @param $attr
     * @param $condition
     * @param bool $single
     * @return int
     */
    public function updateByWhere($attr , $condition,$single = true)
    {
        if($single)
            return Db::table('user')->where($condition)->update($attr);
        return Db::table('user')->where($condition)->update($attr);

    }

    /**
     * @param $id
     * @param $data
     * @return int
     */
    public function updateUser($id,$data)
    {
        return Db::table('user')->where('id','=',$id)
                                       ->update($data);
    }

    /**
     * @param $numbers
     * @return array
     */
    public function getUserByNumbers($numbers)
    {
        $data = [];
        foreach ($numbers as $k => $v)
        {
            $data[] = Db::table('user')->where('number' ,'=', $v)->first();
        }
        return $data;
    }

    /**
     * @param $id
     * @return \Carbon\CarbonInterface|\Hyperf\Utils\HigherOrderTapProxy|int|array|mixed
     */
    public function getNumberById($id)
    {
        $user = Db::table('user')->find($id)->first();
        return $user['number'];
    }

    /**
     * @param $id
     * @return \Hyperf\Database\Model\Model|\Hyperf\Database\Query\Builder|mixed|object|null
     */
    public function getUserById($id)
    {
        return Db::table('user')->find($id);
    }

    /**
     * @return \Hyperf\Utils\Collection
     */
    public function getAllUser()
    {
        return Db::table('user')->get();
    }

    /**
     * @param $value
     * @param null $page
     * @return \Hyperf\Utils\Collection
     */
    public function searchUser($value , $page = null)
    {
        if(!$value)
            return Db::table('user')->get();
        return Db::table('user')->query()
            ->orWhere('number','like','%'.$value.'%')
            ->orWhere('nickname','like','%'.$value.'%')
            ->orWhere('phone','like','%'.$value.'%')
            ->orWhere('email','like','%'.$value.'%')
            ->get();
    }
}