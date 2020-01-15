<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15 0000
 * Time: 21:55
 */

namespace App\Api;
use App\Lib\UserEnum;
use App\Models\GroupModel;
use App\Models\Service\MemberService;
use App\Models\UserGroupModel;
use App\Models\UserModel;
use App\Services\UserCacheService;
use Core\Container\Mapping\Bean;


/**
 * Class UserController
 * @package App\Api
 * @Bean(prefix="/api/im/user")
 */
class UserController extends BaseController
{
    /**
     * 获取群信息 或者获取好友信息
     * RequestMapping(route="friend/info")
     * Strings(from=ValidatorFrom::GET,name="type")
     * Strings(from=ValidatorFrom::GET,name="id")
     * @param Request $request
     */
    public function getInformation($request)
    {
        $data = request()->input();
        $type = $data['type'];
        $id = $data['id'];

        if($type == 'friend')
        {
            $info = (new UserModel())->getUser(['id' => $id]);
            $info['type'] = 'friend';
        }else if($type == 'group')
        {
            //调用群组服务
            $info = (new GroupModel())->getGroup(['id' => $id] , true);
            $info['type'] = 'group';
        }else
        {
            return $this->error('类型错误');
        }

        return $this->success($info);
    }
    /**
     * 用户退出删除用户相关资源
     * RequestMapping(route="user/quit")
     * @param Request $request
     */
    public function userQuit($request)
    {
        $token = request()->input("token");
        $user   = (new UserCacheService)->getUserByToken($token);
        $info = [
            'user' => $user,
            'token' => $token,
        ];
        if($info)
        {
            // 销毁相关缓存
            $this->delCache($info);

            // 给好友发送离线提醒
            $this->offLine($info);
        }
    }
    /*
    * 销毁个人/群组缓存
    */
    private function delCache($info)
    {
        $fd = (new UserCacheService)->getFdByNum($info['user']['number']);
        (new UserCacheService)->delTokenUser($info['token']);
        (new UserCacheService)->delNumberUserOtherInfo($info['user']['number']);
        (new UserCacheService)->delFdToken($fd);
        (new UserCacheService)->delFds($fd);

        //调用群组服务 获取群数量
        $groups = (new GroupModel())->getGroup([['user_number','=',$info['user']['number']]]);
        if($groups)
            foreach ($groups as $val)
                (new UserCacheService())->delGroupFd($val->gnumber, $fd);
    }

    /*
     * 给在线好友发送离线提醒
     */
    private function offLine($user)
    {
        // 从用户服务 获取分组好友
        $friends = (new UserGroupModel())->getAllFriends($user['id']);
        $friends = (new MemberService())->getFriends($friends);

        $server = Cloud::swooleServer();
        $data = [
            'type'      => 'ws',
            'method'    => 'friendOffLine',
            'data'      => [
                'number'    => $user['user']['id'],
                'nickname'  => $user['user']['nickname'],
            ]
        ];
        foreach ($friends as $val)
            foreach ($val['list'] as $v)
                if ($v['status'])
                {
                    $fd = (new UserCacheService)->getFdByNum($v['number']);
                    $server->push($fd, json_encode($data));
                }
    }
    /**
     * 修改用户签名
     * RequestMapping(route="user/sign",method={RequestMethod::GET})
     * Strings(from=ValidatorFrom::GET,name="sign")
     */
    public function editSignature()
    {
        $sign = request()->query('sign');
        $this->getCurrentUser();

        //调用用户服务 更新签名
        $userRes = (new UserModel)->updateByWhere(['sign' => $sign],['id' => $this->user['id']]);


        //更新Redis缓存
        $user = (new UserModel)->updateByWhere(['id' => $this->user['id']],true);
        $user = $userRes['data'];
        (new UserCacheService)->saveTokenToUser(request()->input('token') , $user);
        return $this->success([],'成功');
    }
    /**
     * 查找好友 群
     * RequestMapping(route="find/total",method={RequestMethod::GET})
     * Strings(from=ValidatorFrom::GET,name="value")
     * Strings(from=ValidatorFrom::GET,name="type")
     */
    public function findFriendTotal()
    {
        $type = request()->query('type');
        $value = request()->query('value');
        //搜索用户
        if($type == UserEnum::Friend)
        {
            $res = (new UserModel)->searchUser($value);
        }
        else//搜索群组
        {
            //调用群组服务  搜索群组
            $groupRes = (new GroupModel())->searchGroup($value);
            $res = $groupRes['data'];
        }
        return $this->success(['count' => count($res),'limit' => 16]);
    }
    /**
     * 查找好友 群 统计数量
     * RequestMapping(route="find/friend",method={RequestMethod::GET})
     * Strings(from=ValidatorFrom::GET,name="value")
     * Strings(from=ValidatorFrom::GET,name="type")
     * Strings(from=ValidatorFrom::GET,name="page")
     * @param Request $request
     */
    public function findFriend($request)
    {
        $type = request()->query('type');
        $page = request()->query('page');
        $value = request()->query('value');
        if($type == UserEnum::Friend)
        {
            //搜索用户
            $res = (new UserModel)->searchUser($value );
        }
        else
        {
            //搜索群组
            $groupRes = (new GroupModel())->searchGroup($value);
;
            $res = $groupRes['data'];
        }
        return $this->success($res);

    }
}