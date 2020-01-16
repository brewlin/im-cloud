<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/15 0029
 * Time: 上午 11:02
 */

namespace App\Services;


use App\Models\GroupMemberModel;
use App\Models\GroupModel;
use App\Models\UserModel;
use Core\Container\Mapping\Bean;
use Database\Db;

/**
 * Class GroupService
 * @package App\Services
 * @Bean()
 */
class GroupService
{

    /**
     * @param $id
     * @return array
     */
    public function getGroupMembers($id)
    {
        $owner = bean(GroupModel::class)->getGroupOwner($id);
        //获取群成员
        $memberList = \bean(GroupMemberModel::class)->getGroupMembers($owner['gnumber']);

        //调用用户服务  获取用户列表
        $userRes = \bean(UserModel::class)->getUserByNumbers($memberList);
        $list = $userRes['data'];
        return compact('owner','list');

    }

    /**
     * @param $id
     * @param $number
     * @return int
     */
    public function leaveGroup($id,$number)
    {
        $groupNumber = \bean(GroupMemberModel::class)->getNumberById($id);
        return \bean(GroupMemberModel::class)->delMemberById($number,$groupNumber);
    }

    /**
     * @param $data
     * @param $number
     * @param $userNumber
     * @return bool
     * @throws \Exception
     */
    public function createGroup($data,$number ,$userNumber)
    {
        // 保存群信息，并加入群
        $group_data = [
            'gnumber'       => $number,
            'user_number'   => $userNumber,
            'ginfo'         => $data['des'],
            'gname'         => $data['des'],
            'groupname' => $data['groupName'],//群名称
            'approval' => $data['approval'],//验证方式 需要验证 不需要验证
            'number' => $data['number'],//群上限人数
        ];
        $member_data = [
            'gnumber'       => $number,
            'user_number'   => $userNumber,
        ];
        Db::beginTransaction();
        try
        {
            $id = bean(GroupModel::class)->newGroup($group_data);
            \bean(GroupMemberModel::class)->newGroupMember($member_data);
            Db::commit();
        }catch (\Throwable $e)
        {
            Db::rollback();
            throw new \Exception($e->getMessage());
        }
        return $id;
    }

}