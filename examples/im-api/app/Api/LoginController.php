<?php
/**
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15 0000
 * Time: 21:50
 */

namespace App\Api;

use App\Lib\Common;
use App\Models\UserGroupModel;
use App\Models\UserModel;
use App\Services\UserCacheService;
use Core\Container\Mapping\Bean;
use Database\Db;

/**
 * Class LoginController
 * @Bean("/")
 */
class LoginController extends BaseController
{
    /**
     * 用户登录
     * 验证通过后，将信息存入 redis
     * RequestMapping(route="/login")
     * Strings(from=ValidatorFrom::POST,name="email")
     * Strings(from=ValidatorFrom::POST,name="password")
     */
    public function login()
    {
        $email = request()->post('email');
        $password = request()->post('password');
        // 查询用户是否已经存在
        $user = \bean(UserModel::class)->getUser(['email' => $email],true);
        if (empty($user))
            throw new \Exception( '无效账号');
        // 比较密码是否一致
        if (strcmp(md5($password), $user['password']))
            throw new \Exception('密码错误');

        // 更新登录时间
        $update = [ 'last_login' => time()];
        \bean(UserModel::class)->updateUser($user['id'], $update);

        // 生成 token
        $token = Common::getRandChar(16);
        // 将用户信息存入缓存
        \bean(UserCacheService::class)->saveNumToToken($user['number'], $token);
        \bean(UserCacheService::class)->saveTokenToUser($token,$user);

//        $userFd = \bean(UserCacheService::class)->getFdByNum($user['number']);
//        if ($userFd)
//            Cloud::swooleServer()->push($userFd, json_encode(['type' => 'ws', 'method' => 'belogin', 'data' => 'logout']));
//        }
        return $this->success($token,'登录成功');
    }
    /**
     * RequestMapping(route="/register")
     * Strings(from=ValidatorFrom::POST,name="email")
     * Strings(from=ValidatorFrom::POST,name="password")
     * Strings(from=ValidatorFrom::POST,name="nickname")
     * Strings(from=ValidatorFrom::POST,name="repassword")
     * 用户注册
     */
    public function register()
    {
        // 验证
        $email = request()->post('email');
        $nickname = request()->post('nickname');
        $password = request()->post('password');
        $repassword = request()->post('repassword');

        // 判断两次密码是否输入一致
        if (strcmp($password, $repassword))
            throw new \Exception("两次密码输入不一致");

        // 查询用户是否已经存在
        /** @var UserModel $user */
        $user = \bean(UserModel::class)->getUser(['email' => $email]);
        if (!empty($user))
            throw new \Exception("该用户已存在");

        // 生成唯一number
        $number = Common::generate_code();
        /** @var UserModel $usermodel */
        $usermodel = \bean(UserModel::class);
        while ($usermodel->getUser(['number' => $number]))
            $number = Common::generate_code();

        // 入库
        $data = [
            'email' => $email,
            'password' => md5($password),
            'nickname' => $nickname,
            'number' => $number,
            'username' => $nickname
        ];
        Db::beginTransaction();
        try
        {
            $uid = $usermodel->newUser($data);
            \bean(UserGroupModel::class)->addGroup($uid,"我的好友");
            Db::commit();
        }catch (\Throwable $e)
        {
            Db::rollback();
            return $this->error('注册失败');
        }
        return $this->success('','注册成功');
    }


}