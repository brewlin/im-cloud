<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15 0000
 * Time: 21:54
 */

namespace App\Api;
use App\Models\GroupRecordModel;
use App\Models\UserGroupModel;
use App\Models\UserRecordModel;
use Core\Container\Mapping\Bean;
use Database\Db;

/**
 * Class UserRecordControoler
 * @package App\Api
 * @Bean(prefix="/api/im")
 */
class UserRecordController extends BaseController
{

    /**
     * @return mixed
     * RequestMapping(route="record",method={RequestMethod::GET})
     * Strings(from=ValidatorFrom::GET,name="id")
     * Strings(from=ValidatorFrom::GET,name="type")
     * Strings(from=ValidatorFrom::GET,name="token")
     */
    public function getChatRecordByToken()
    {
        $this->getCurrentUser();
        $uid = $this->user['id'];
        $data = \request()->query();
        if($data['type'] == 'friend')
        {
            $res = \bean(UserRecordModel::class)->getAllChatRecordById($uid , $data['id']);
        }else if($data['type'] == 'group')
        {
            $group = Db::table('group')->find($data['id'])->first();
            $groupNumber = $group['number'];
            $res = (new GroupRecordModel())->getAllChatRecordById($uid , $groupNumber);
        }else
        {
            $res = \bean(UserRecordModel::class)->getAllChatRecordById($uid , $data['id']);
        }
        return $this->success($res);
    }

    /**
     * 更新已读消息
     * RequestMapping(route="chat/record/read",method={RequestMethod::POST})
     * Strings(from=ValidatorFrom::POST,name="uid")
     * Strings(from=ValidatorFrom::POST,name="type")
     * Strings(from=ValidatorFrom::POST,name="token")
     * @param Request $request
     */
    public function updateIsReadChatRecord()
    {
        $this->getCurrentUser();
        $where = ['friend_id' => $this->user['id'],'user_id' => request()->post('uid'),'is_read' => 0];
        $data = ['is_read' => 1];
        $type = request()->post('type');
        \bean(UserRecordModel::class)->updateByWhere($where,$data);
        return $this->success([],'收取消息成功');
    }
}