<?php
/***
 * Created by PhpStorm.
 * User: liuxiaodong
 * Date: 2020/1/15
 * Time: 上午10:13
 */

namespace App\Services;



use Core\Container\Mapping\Bean;
use ImRedis\Redis;

/**
 * Class UserCacheService
 * @package App\Services
 * @Bean()
 */
class UserCacheService {
	/**
	 *  保存 token => userInfo
	 */
	public function saveTokenToUser($token, $user) {
		if (!is_array($user)) {
			$user = json_decode(json_encode($user), true);
		}
		$key = \config('cache.cacheName.token_user');
		$key = sprintf($key, $token);
		return Redis::hMSet($key, $user);
	}

	/**
	 * 保存 number => token
	 */
	public function saveNumToToken($number, $token) {
		$key = \config('cache.cacheName.number_userOtherInfo');
		$key = sprintf($key, $number);
		return Redis::hSet($key, 'token', $token);
	}

	/**
	 * 根据number获取token
	 */
	public function getTokenByNum($number) {
		$key = \config('cache.cacheName.number_userOtherInfo');
		$key = sprintf($key, $number);
		return Redis::hGet($key, 'token');
	}

	/**
	 * 根据 token 获得 number 信息
	 */
	public function getNumByToken($token) {
		$key = \config('cache.cacheName.token_user');
		$key = sprintf($key, $token);
		return Redis::hGet($key, 'number');
	}
	/**
	 * 根据token获取id信息
	 */
	public function getIdByToken($token) {
		$res = self::getUserByToken($token);
		return $res['id'];
	}
	/**
	 *
	 * 保存 number => fd
	 */
	public function saveNumToFd($number, $fd) {
		$key = \config('cache.cacheName.number_userOtherInfo');
		$key = sprintf($key, $number);
		return Redis::hSet($key, 'fd', $fd);
	}

	/**
	 * 根据 number 获取 fd
	 */
	public function getFdByNum($number) {
		$key = \config('cache.cacheName.number_userOtherInfo');
		$key = sprintf($key, $number);

		return Redis::hGet($key, 'fd');
	}

	/**
	 * 根据 token 获取所有 user 信息
	 */
	public function getUserByToken($token) {
		$key = \config('cache.cacheName.token_user');
		$key = sprintf($key, $token);
		return Redis::hGetAll($key);
	}

	/**
	 * 保存好友请求的双方验证信息
	 */
	public function saveFriendReq($from_num, $to_num) {
		$key = \config('cache.cacheName.friend_req');
		$key = sprintf($key, $from_num);

		return Redis::set($key, $to_num);
	}

	/**
	 * 获取好友验证
	 */
	public function getFriendReq($from_num) {
		$key = \config('cache.cacheName.friend_req');
		$key = sprintf($key, $from_num);

		return Redis::get($key);
	}

	/**
	 * fd => token
	 */
	public function saveTokenByFd($fd, $token) {
		$key = \config('cache.cacheName.fd_token');
		$key = sprintf($key, $fd);

		return Redis::set($key, $token);
	}

	/**
	 * 获取fd => token
	 */
	public function getTokenByFd($fd) {
		$key = \config('cache.cacheName.fd_token');
		$key = sprintf($key, $fd);

		return Redis::get($key);
	}

	public function saveFds($fd) {
		$key = \config('cache.cacheName.all_fd');

		return Redis::sAdd($key, $fd);
	}
	public function getFdFromSet() {
		$key = \config('cache.cacheName.all_fd');

		return Redis::sRandMember($key);
	}

	public function setGroupFds($gnumber, $fd) {
		$key = \config('cache.cacheName.group_number_fd');
		$key = sprintf($key, $gnumber);

		return Redis::lPush($key, $fd);
	}

	public function getGroupFdsLen($gnumber) {
		$key = \config('cache.cacheName.group_number_fd');
		$key = sprintf($key, $gnumber);

		return Redis::lLen($key);
	}

	public function getGroupFd($gnumber, $index) {
		$key = \config('cache.cacheName.group_number_fd');
		$key = sprintf($key, $gnumber);
		return Redis::lIndex($key, $index);
	}

	public function delGroupFd($gnumber, $fd) {
		$key = \config('cache.cacheName.group_number_fd');
		$key = sprintf($key, $gnumber);
		return Redis::lRem($key, $fd,0);
	}
	/**
	 * 销毁
	 */
	public function delTokenUser($token) {
		$key = \config('cache.cacheName.token_user');
		$key = sprintf($key, $token);
		self::delHashKey($key);
	}

	public function delNumberUserOtherInfo($number) {
		$key = \config('cache.cacheName.number_userOtherInfo');
		$key = sprintf($key, $number);
		self::delHashKey($key);
	}

	public function delFdToken($fd) {
		$key = \config('cache.cacheName.fd_token');
		$key = sprintf($key, $fd);

		return Redis::del($key);
	}

	public function delFriendReq($from_num) {
		$key = \config('cache.cacheName.friend_req');
		$key = sprintf($key, $from_num);

		return Redis::del($key);
	}

	public function delFds($fd) {
		$key = \config('cache.cacheName.all_fd');

		return Redis::sRem($key, $fd);
	}

	/**
	 * 删除 hash 键下的所有值
	 */
	private function delHashKey($key) {

		$res = Redis::hKeys($key);
		if ($res) {
			foreach ($res as $val) {
				Redis::hDel($key, $val);
			}
		}

	}
}