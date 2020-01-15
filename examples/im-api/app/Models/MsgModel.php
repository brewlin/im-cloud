<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/15 0015
 * Time: 下午 19:10
 */

namespace App\Models;
use Core\Container\Mapping\Bean;
use Database\Db;

/**
 * Class MsgModelDao
 * @package App\Models\Dao
 * @Bean()
 */
class MsgModel
{
    /**
     * 根据用户id获取消息
     */
    public function getDataByUserId($userId)
    {
        $msg = Db::table('msg')->orWhere('from','=',$userId)
            ->orWhere('to','=',$userId)
            ->orderBy('send_time','desc')
            ->get()
            ->toArray();
        foreach ($msg as $k => $v)
        {
            $msg[$k]['to'] = Db::table('user')->where('id' ,'=', $v['to'])->first();
            $msg[$k]['from'] = Db::table('user')->where('id','=', $v['from'])->first();
        }
        return $msg;
    }
    /**
     * 添加信息
     */
    public function addMsgBox($data)
    {
        return Db::table('msg')->insert($data);
    }
    public function getDataById($id)
    {
        return Db::table('msg')->find($id)->get()->toArray();
    }
    public function updateById($id , $where)
    {
        return Db::table('msg')->where(['id' => $id])->update($where);
    }
    public function updateByWhere($where ,$update)
    {
        return Db::table('msg')->where($where)->update($update);
    }
    public function getOneByWhere($where)
    {
        return Db::table('msg')->where($where)->get()->toArray();
    }
}