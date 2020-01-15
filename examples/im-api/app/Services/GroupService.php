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

    public function getGroupMembers($id)
    {
        $owner = (new GroupModel())->getGroupOwner($id);
        //获取群成员
        $memberList = (new GroupMemberModel())->getGroupMembers($owner['gnumber']);

        //调用用户服务  获取用户列表
        $userRes = (new UserModel())->getUserByNumbers($memberList);
        $list = $userRes['data'];
        return compact('owner','list');

    }

    public function leaveGroup($id,$number)
    {
        $groupNumber = (new GroupMemberModel())->getNumberById($id);
        return (new GroupMemberModel())->delMemberById($number,$groupNumber);
    }
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
            $id = (new GroupModel())->newGroup($group_data);
            (new GroupMemberModel())->newGroupMember($member_data);
            Db::commit();
        }catch (\Throwable $e)
        {
            Db::rollback();
            throw new \Exception($e->getMessage());
        }
        return $id;
    }

}