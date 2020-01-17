<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15 0000
 * Time: 21:54
 */

namespace App\Api;

use App\Models\UserGroupModel;
use App\Services\UserCacheService;
use Core\Container\Mapping\Bean;

/**
 * Class UserGroupController
 * @package App\Api
 * @Bean(prefix="/api/im/group")
 */
class UserGroupController extends BaseController
{
    /**
     * 分组名添加
     * RequestMapping(route="user/add",method={RequestMethod::GET})
     * Strings(from=ValidatorFrom::GET,name="token")
     * Strings(from=ValidatorFrom::GET,name="groupname")
     * @param Request $request
     */
    public function addMyGroup()
    {
        $token = request()->query('token');
        $groupname = request()->query('groupname');

        //调用user服务
        $id = \bean(UserCacheService::class)->getIdByToken($token);
        $groupId = \bean(UserGroupModel::class)->addGroup($id , $groupname);

        return $this->success(['id' => $groupId , 'groupname' => $groupname]);
    }
    /**
     * 分组名修改
     * RequestMapping(route="user/edit",method={RequestMethod::GET})
     * Strings(from=ValidatorFrom::GET,name="id")
     * Strings(from=ValidatorFrom::GET,name="groupname")
     * @param Request $request
     */
    public function editMyGroup()
    {
        $data = request()->query();

        //调用用户服务
        $res = \bean(UserGroupModel::class)->updateByWhere(['groupname' => $data['groupname']],['id' => $data['id']]);
        if(!$res)
            return $this->error([],'修改失败');
        return $this->success([],'修改成功');
    }
    /**
     * 删除分组名
     * RequestMapping(route="user/del",method={RequestMethod::GET})
     * Strings(from=ValidatorFrom::GET,name="id")
     * @param Request $request
     */
    public function delMyGroup()
    {
        $this->getCurrentUser();
        $res = \bean(UserGroupModel::class)->delGroup(request()->query('id'),$this->user);
        if($res)
            return $this->error([],'删除成功');
        return $this->error([],'删除失败');
    }
}