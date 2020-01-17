<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15 0000
 * Time: 21:38
 */

namespace App\Api;

use App\Lib\Common;
use App\Models\GroupMemberModel;
use App\Models\GroupModel;
use App\Services\GroupService;
use App\Services\UserCacheService;
use Core\Container\Mapping\Bean;

/**
 * Class Group
 * @package App\Api
 * @Bean()
 */
class GroupController extends BaseController
{
    /**
     * RequestMapping(route="/api/im/members",method={RequestMethod::GET})
     * @param Request $request
     */
    public function getMembers()
    {
        $id = request()->query('id');
        if(empty($id))return $this->error('require id arg');
        //调用群组服务 获取群信息
        $groupRes = bean(GroupService::class)->getGroupMembers($id);
  

        return $this->success($groupRes['data']);
    }
    /**
     * 离开群组
     * RequestMapping(route="group/leave",method={RequestMethod::GET})
     * Strings(from=ValidatorFrom::GET,name="id")
     * @param Request $request
     */
    public function leaveGroup()
    {
        $this->getCurrentUser();
        $number = $this->user['number'];
        $id = request()->query('id');

        //调用群组服务 退出群组
        bean(GroupService::class)->leaveGroup($id,$number);

        return $this->success('','退出成功');
    }
    /**
     * 检查用户是否可以继续创建群
     * RequestMapping(route="group/check",method={RequestMethod::GET})
     */
    public function checkUserCreateGroup()
    {
        $this->getCurrentUser();
        //调用群组服务 获取群组信息
        $groupRes = bean(GroupModel::class)->getGroup(['user_number' => $this->user['number']]);
        $list = $groupRes['data'];
        if(count($list) > 50)
            return $this->error('超过最大建群数量');
        return $this->success();
    }
    /**
     * 创建群
     * RequestMapping(route="group/create",method={RequestMethod::POST})
     * Strings(from=ValidatorFrom::POST,name="groupName")
     * Strings(from=ValidatorFrom::POST,name="des")
     * Strings(from=ValidatorFrom::POST,name="number")
     * Strings(from=ValidatorFrom::POST,name="approval")
     * @param Request $request
     */
    public function createGroup()
    {
        $data = request()->post();
        $this->getCurrentUser();
        // 生成唯一群号
        $number = Common::generate_code(8);

        //调用群组服务 创建群
        $id = bean(GroupService::class)->createGroup($data,$number,$this->user['number']);
        
        $sendData  = [
            'id'            => $id,
            'avatar'         => '/timg.jpg',
            'groupname'     => $data['groupName'],
            'type'          => 'group',
            'gnumber'       => $number

        ];
        // 创建缓存
        \bean(UserCacheService::class)->setGroupFds($number, $this->user['fd']);
        $server = Cloud::swooleServer();
        $server->push($this->user['fd'] , json_encode(['type'=>'ws','method'=> 'newGroup','data'=> $sendData]));
        $server->push($this->user['fd'] , json_encode(['type'=>'ws','method'=> 'ok','data'=> '创建成功']));

        return $this->success(['groupid' => $number,'groupName' => $data['groupName']]);
    }
}